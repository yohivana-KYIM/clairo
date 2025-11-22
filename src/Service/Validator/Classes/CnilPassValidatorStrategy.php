<?php

namespace App\Service\Validator\Classes;

use App\Service\Validator\Interfaces\PassValidatorStrategyInterface;

class CnilPassValidatorStrategy implements PassValidatorStrategyInterface
{
    private array $errors;

    public function validate(string $password): bool
    {
        $this->flushErrors();
        if (strlen($password) < 8) {
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
