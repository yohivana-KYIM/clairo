<?php

namespace App\Service\Validator\Interfaces;

/**
 * Interface for password validation strategies.
 */
interface PassValidatorStrategyInterface
{
    /**
     * Validates the given password.
     *
     * @param string $password
     * @return bool True if valid, false otherwise.
     */
    public function validate(string $password): bool;
    public function getEncounteredErrors(): array;
    public function flushErrors(): void;
}
