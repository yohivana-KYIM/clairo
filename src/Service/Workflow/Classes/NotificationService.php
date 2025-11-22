<?php

namespace App\Service\Workflow\Classes;

use App\Entity\Message;
use App\Entity\User;
use App\Service\SettingsService;
use App\Service\Workflow\Interfaces\EmailContentProviderInterface;
use App\Service\Workflow\Interfaces\NotificationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class NotificationService implements NotificationServiceInterface
{
    private $logger;

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly RequestStack $requestStack,
        private readonly EmailContentProviderInterface $emailContentProvider,
        private readonly EntityManagerInterface $entityManager,
        private readonly SettingsService $settingsService,
    )
    {
    }

    /**
     * Send a user-to-user message or app notification.
     */
    public function sendMessageOrNotification(?User $sender, User $receiver, string $content, string $type = 'message'): void
    {
        $message = new Message();
        $message->setSender($sender); // Null for app notifications
        $message->setReceiver($receiver);
        $message->setContent($content);
        $message->setIsRead(false);
        $message->setCreatedAt(new \DateTimeImmutable());
        $message->setType($type);

        $this->entityManager->persist($message);
        $this->entityManager->flush();
    }

    public function sendAppNotification(User $receiver, string $content): void
    {
        $appUser = $this->entityManager->getRepository(User::class)->findByRole('ROLE_SYSTEM');
        if (!$appUser) {
            throw new \InvalidArgumentException(User::APP_USER_ID . " is invalid.");
        }

        $this->sendMessageOrNotification($appUser, $receiver, $content, 'notification');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailNotification(User $user): void
    {
        if (!filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email invalide: " . $user->getEmail());
        }
        
        $emailContent = $this->emailContentProvider->getEmailContent($user);

        $email = (new Email())
            ->from($this->settingsService->get('system_email'))
            ->to($user->getEmail())
            ->subject('Notification Subject')
            ->html($emailContent);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error("Échec de l'envoi du mail : " . $e->getMessage());
            throw $e;
        }
    }

    public function sendUINotification(User $user): void
    {
        $session = $this->requestStack->getSession();
        $session->getFlashBag()->add('notification', 'You have a new notification.');
    }

    /**
     * Valide l'adresse email (string ou Address) et retourne une instance d'Address
     *
     * @param string|Address $email
     * @param bool $isRequired Si l'email doit être non vide et valide
     * @return Address
     * @throws InvalidArgumentException Si l'email est vide ou invalide
     */
    private function validateEmail(string|Address $email, bool $isRequired = true): Address
    {
        if ($email instanceof Address) {
            return $email;
        }

        if (empty($email)) {
            if ($isRequired) {
                throw new InvalidArgumentException('Une adresse email requise est vide.');
            } else {
                return new Address(''); // Valeur vide, accepté si non obligatoire
            }
        }

        return new Address(trim($email,'; '));
    }

    /**
     * Envoie un email avec validation des paramètres fournis
     *
     * @param string|Address $from Adresse email de l'expéditeur
     * @param string $to Adresse email du destinataire
     * @param string $subject Sujet de l'email
     * @param string|null $cc Adresse email pour copie carbone (optionnel)
     * @param string|null $template Identifiant ou chemin du template (optionnel)
     * @param array|null $templateVars Variables pour le modèle utilisé (optionnel)
     * @return void
     * @throws InvalidArgumentException|TransportExceptionInterface Si une adresse email est invalide
     */
    public function sendTemplatedEmail(
        string|Address $from,
        string $to,
        string $subject,
        array|string|null $cc = null,
        ?string $template = '',
        ?array $templateVars = [],
        ?string $attachmentPath = null,
        ?string $attachmentName = null
    ): void {
        // 1. Validation des adresses email
        $fromAddress = $this->validateEmail($from);
        $toAddress = $this->validateEmail($to);

        $email = (new TemplatedEmail())
            ->from($fromAddress)
            ->to($toAddress)
            ->subject($subject);

        // Ajout du modèle (si fourni)
        if (!empty($template)) {
            $email->htmlTemplate($template);
            $email->context($templateVars ?? []);
        }

        // Ajout des adresses CC (si fournies)
        if (!empty($cc)) {
            $ccList = is_array($cc) ? $cc : explode(';', $cc);
            $ccList = array_filter(array_map('trim', $ccList));
            $ccList = array_map(fn ($addr) => $this->validateEmail($addr, false), $ccList);

            if (!empty($ccList)) {
                $email->cc(...$ccList); // ✅ décompresse le tableau en arguments
            }
        }

        // Ajout de la pièce jointe (si fournie)
        if ($attachmentPath && file_exists($attachmentPath)) {
            $email->attachFromPath(
                $attachmentPath,
                $attachmentName ?: basename($attachmentPath)
            );
        }

        // 3. Envoi de l'email
        $this->mailer->send($email);
    }
}