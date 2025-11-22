<?php

// src/Controller/Security/RoleSwitchController.php
namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class RoleSwitchController extends AbstractController
{
    #[Route('/switch-role/{role}', name: 'app_switch_role')]
    public function switchRole(string $role, SessionInterface $session, Security $security): RedirectResponse
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $roles = $security->getToken()?->getRoleNames() ?? [];

        if (!in_array($role, $roles, true)) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas adopter ce rôle.");
        }

        $session->set('active_role', $role);

        return $this->redirectToRoute('app_home');
    }

    #[Route('/unswitch-role', name: 'app_unswitch_role')]
    public function unswitchRole(SessionInterface $session): RedirectResponse
    {
        $session->remove('active_role');

        return $this->redirectToRoute('app_home');
    }
}
