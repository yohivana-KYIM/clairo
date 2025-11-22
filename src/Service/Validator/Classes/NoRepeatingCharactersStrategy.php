<?php

namespace App\Service\Validator\Classes;

use App\Service\Validator\Interfaces\PassValidatorStrategyInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('validator.strategy')]
class NoRepeatingCharactersStrategy implements PassValidatorStrategyInterface
{
    private array $errors = [];

    public function validate(string $password): bool
    {
        $this->flushErrors();
        // Corrected regex to match three or more consecutive repeating characters
        if (preg_match('/(.)\1{2,}/', $password)) {
            $this->addError('le mot de passe ne doit pas contenir plus de 3 caractères repétitifs');
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
