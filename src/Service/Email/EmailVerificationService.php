<?php

namespace App\Service\Email;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class EmailVerificationService
{
    private readonly EmailVerifier $emailVerifier;
    private readonly EntityManagerInterface $entityManager;

    public function __construct(EmailVerifier $emailVerifier, EntityManagerInterface $entityManager)
    {
        $this->emailVerifier = $emailVerifier;
        $this->entityManager = $entityManager;
    }

    public function sendVerificationEmail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from('cleo@fluxel.fr')
            ->to($user->getEmail())
            ->subject('Merci de confirmer votre email !')
            ->htmlTemplate('registration/confirmation_email.html.twig');

        $this->emailVerifier->sendEmailConfirmation('app_register_verify_email', $user, $email);
    }

    /**
     * @throws Exception
     */
    public function verifyEmail(Request $request): void
    {
        $id = $request->query->get('id');
        if (!$id) {
            throw new Exception('ID utilisateur manquant.');
        }

        $user = $this->entityManager->getRepository(User::class)->find($id);
        if (!$user) {
            throw new Exception('Utilisateur introuvable.');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            throw new Exception($exception->getReason());
        }
    }
}
