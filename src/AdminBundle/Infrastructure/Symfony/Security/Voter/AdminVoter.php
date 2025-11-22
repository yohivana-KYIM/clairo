<?php

namespace App\AdminBundle\Infrastructure\Symfony\Security\Voter;

use App\AdminBundle\Domain\Model\Entity;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['ENTITY_EDIT', 'ENTITY_DELETE']) && $subject instanceof Entity;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Only allow edit/delete if the user owns the entity or is an admin
        return \in_array('ROLE_ADMIN', $user->getRoles(), true)
            || (\is_object($subject) && \method_exists($subject, 'getOwnerId') && $subject->getOwnerId() === $user->getId());
    }
}
