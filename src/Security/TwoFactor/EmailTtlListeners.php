<?php
// src/Security/TwoFactor/EmailTtlListeners.php
namespace App\Security\TwoFactor;

use App\Entity\User;
use DateMalformedStringException;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvent;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class EmailTtlListeners
{
    public function __construct(
        private readonly EntityManagerInterface                 $em,
        #[Autowire('%env(int:APP_2FA_EMAIL_TTL)%')] private readonly int $ttl = 300
    ) {}

    /**
     * @throws DateMalformedStringException
     */
    #[AsEventListener(event: TwoFactorAuthenticationEvents::ATTEMPT, priority: 100)]
    public function onAttempt(TwoFactorAuthenticationEvent $event): void
    {
        $user = $event->getToken()->getUser();

        // On cible uniquement le cas OTP e-mail : l’utilisateur a un code en cours
        if ($user instanceof User && $user->getAuthCode() !== null && $user->isEmailAuthCodeExpired($this->ttl)) {
            $user->clearEmailAuthCode();
            $this->em->flush();

            throw new AuthenticationException('Le code e-mail a expiré. Veuillez demander un nouveau code.');
        }
    }

    // Nettoie le code après un succès 2FA
    #[AsEventListener(event: TwoFactorAuthenticationEvents::SUCCESS)]
    public function onSuccess(TwoFactorAuthenticationEvent $event): void
    {
        $user = $event->getToken()->getUser();
        if ($user instanceof User) {
            $user->clearEmailAuthCode();
            $this->em->flush();
        }
    }
}
