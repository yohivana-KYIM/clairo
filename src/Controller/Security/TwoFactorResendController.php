<?php

namespace App\Controller\Security;

use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Email\Generator\CodeGeneratorInterface as CodeGenerator;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface as Email2FAUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class TwoFactorResendController extends AbstractController
{
    /**
     * @param CodeGenerator $emailCodeGenerator
     * @param EntityManagerInterface $em
     */
    public function __construct(
        private readonly CodeGenerator $emailCodeGenerator,
        private readonly EntityManagerInterface $em,
        private readonly TokenStorageInterface $tokenStorage
    ) {}

    #[Route('/2fa/resend', name: '2fa_resend_code', methods: ['POST'])]
    public function resend(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['success' => false, 'message' => 'Utilisateur non connecté'], 400);
        }

        // Sécurisation : vérifier que le user supporte vraiment Email 2FA
        if (!$user instanceof Email2FAUser) {
            return new JsonResponse(['success' => false, 'message' => 'User incompatible avec la stratégie email'], 400);
        }

        $token = $this->tokenStorage->getToken();
        if (!$token instanceof TwoFactorTokenInterface) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Aucune stratégie MFA active détectée.'
            ], 400);
        }

        // Scheb 2FA stocke déjà le provider actif dans la session
        $activeProvider = $token->getCurrentTwoFactorProvider();

        if (!$activeProvider) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Aucune stratégie MFA active détectée'
            ], 400);
        }

        // ---------- EMAIL ----------
        if ($activeProvider === 'email') {

            if (!$user->isEmailAuthEnabled()) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'La stratégie email n’est pas activée'
                ], 400);
            }

            // Générer + envoyer un nouveau code
            $this->emailCodeGenerator->generateAndSend($user);

            $this->em->flush();

            return new JsonResponse([
                'success' => true,
                'strategy' => 'email',
                'message' => 'Un nouveau code vous a été envoyé'
            ]);
        }

        // ---------- TOTP ----------
        if ($activeProvider === 'totp') {
            return new JsonResponse([
                'success' => false,
                'strategy' => 'totp',
                'message' => 'Impossible de renvoyer un code TOTP.'
            ], 400);
        }

        // ---------- GOOGLE ----------
        if ($activeProvider === 'google') {
            return new JsonResponse([
                'success' => false,
                'strategy' => 'google',
                'message' => 'Les apps Google Authenticator génèrent automatiquement les codes.'
            ], 400);
        }

        return new JsonResponse([
            'success' => false,
            'message' => 'Stratégie inconnue'
        ], 400);
    }
}
