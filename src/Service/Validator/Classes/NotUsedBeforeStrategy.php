<?php

namespace App\Service\Validator\Classes;

use App\Entity\User;
use App\Service\Validator\Interfaces\PassValidatorStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Strategy for validating that the password has not been used before.
 */
#[AutoconfigureTag('validator.strategy')]
class NotUsedBeforeStrategy implements PassValidatorStrategyInterface
{

    private array $errors;

    public function __construct(private readonly Security $security)
    {
    }

    public function validate(string $password): bool
    {
        $this->flushErrors();
        // Fetch the logged-in user
        $user = $this->security->getUser();
        if (!($user instanceof User)) return true;

        // Check if the password is already used
        $previousPasswords = $user->getPasswordHistory();

        if (in_array($password, $previousPasswords, true)) {
            return false;
        }

        return true;
    }

    public function getEncounteredErrors(): array
    {
        return $this->errors;
    }

    public function flushErrors(): void
    {
        $this->errors = [];
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
}