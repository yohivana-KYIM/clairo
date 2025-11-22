<?php

namespace App\MultiStepBundle\Default;

use LogicException;

class ErrorState extends DefaultWorkflowState
{
    private string $errorMessage;

    public function __construct(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function getCurrentStep(): DefaultStepInterface
    {
        throw new LogicException('Error state: ' . $this->errorMessage);
    }

    public function nextStep(): DefaultWorkflowState
    {
        throw new LogicException('Cannot proceed from error state.');
    }

    public function isComplete(): bool
    {
        return false;
    }
}