<?php

namespace App\Controller;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DebugController extends AbstractController
{
    #[Route('/debug-auth', name: 'debug_auth')]
    public function checkAuthState(TokenStorageInterface $tokenStorage): Response
    {
        $token = $tokenStorage->getToken();

        if (!$token) {
            return new Response('❌ Aucun token, utilisateur déconnecté.', Response::HTTP_UNAUTHORIZED);
        }

        if ($token instanceof TwoFactorToken) {
            return new Response('✅ Utilisateur en attente de validation 2FA.');
        }

        return new Response('⚠️ Utilisateur complètement authentifié ou erreur.');
    }

    #[Route('/debug-session', name: 'debug_session')]
    public function testSession(SessionInterface $session): Response
    {
        if (!$session->has('test_value')) {
            $session->set('test_value', bin2hex(random_bytes(8)));
        }

        return new Response('Valeur de session : ' . $session->get('test_value'));
    }
}