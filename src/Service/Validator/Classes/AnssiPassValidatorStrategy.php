<?php

namespace App\Service\Validator\Classes;

use App\Service\Validator\Interfaces\PassValidatorStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('validator.strategy')]
class AnssiPassValidatorStrategy implements PassValidatorStrategyInterface
{
    public array $errors = [];
    public function validate(string $password): bool
    {
        $this->flushErrors();
        if (strlen($password) < 12) {
            $this->addError('le mot de passe doit avoir au moins 12 caractères');
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $this->addError('le mot de passe doit contenir les lettres majuscules');
        }

        if (!preg_match('/[a-z]/', $password)) {
            $this->addError('le mot de passe doit contenir les lettres minuscules');
        }
        if (!preg_match('/\\d/', $password)) {
            $this->addError('le mot de passe doit contenir les chiffres');
        }
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $this->addError('le mot de passe doit contenir les caractères spéciaux (!@#$%^&*(),.?":{}|<>)');
        }

        return empty($this->errors);
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
