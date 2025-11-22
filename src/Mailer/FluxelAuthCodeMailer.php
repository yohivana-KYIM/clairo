<?php

declare(strict_types=1);

namespace App\Mailer;

use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

#[AsAlias(id: 'app.fluxel_auth_code_mailer', public: true)]
class FluxelAuthCodeMailer implements AuthCodeMailerInterface
{
    private Address|string|null $senderAddress = null;

    public function __construct(
        private readonly MailerInterface $mailer,
        #[Autowire(param: 'scheb_two_factor.email.sender_email')]
        ?string $senderEmail,
        #[Autowire(param: 'scheb_two_factor.email.sender_name')]
        ?string $senderName,
    ) {
        if ($senderEmail && $senderName) {
            $this->senderAddress = new Address($senderEmail, $senderName);
        } elseif ($senderEmail) {
            $this->senderAddress = $senderEmail;
        }
    }

    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $authCode = $user->getEmailAuthCode();
        if (null === $authCode) {
            return;
        }

        $email = (new TemplatedEmail())
            ->to($user->getEmailAuthRecipient())
            ->subject('🔐 Votre code de vérification – Cléo / Fluxel')
            ->htmlTemplate('security/2fa/email_template.html.twig')
            ->context([
                'authenticationCode' => $authCode,
            ]);

        if ($this->senderAddress) {
            $email->from($this->senderAddress);
        }

        $this->mailer->send($email);
    }
}
