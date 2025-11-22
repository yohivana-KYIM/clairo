<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RoleContext
{
    public function __construct(
        private Security $security,
        private SessionInterface $session
    ) {}

    public function getActiveRole(): ?string
    {
        $role = $this->session->get('active_role');
        $userRoles = $this->security->getToken()?->getRoleNames() ?? [];

        return in_array($role, $userRoles, true) ? $role : null;
    }

    public function hasActiveRole(string $role): bool
    {
        return $this->getActiveRole() === $role;
    }

    public function reset(): void
    {
        $this->session->remove('active_role');
    }
}
