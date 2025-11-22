<?php

namespace App\Service\Validator\Classes;

use App\Service\Validator\Interfaces\PassValidatorStrategyInterface;

class CompositePassValidator implements PassValidatorStrategyInterface
{
    /**
     * @var PassValidatorStrategyInterface[]
     */
    private readonly iterable $strategies;
    private array $errors;

    public function __construct(iterable $strategies)
    {
        $this->strategies = $strategies;
    }

    public function validate(string $password): bool
    {
        $this->flushErrors();
        foreach ($this->strategies as $strategy) {
            if (!$strategy->validate($password)) {
                $this->addErrors($strategy->getEncounteredErrors());
            }
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

    public function addErrors(array $errors): void
    {
        $this->errors = array_merge($this->errors, $errors);
    }
}
