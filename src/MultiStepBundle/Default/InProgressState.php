<?php

namespace App\MultiStepBundle\Default;

class InProgressState extends DefaultWorkflowState
{
    public function getCurrentStep(): DefaultStepInterface
    {
        // Logic to get current step in progress
    }

    public function nextStep(): DefaultWorkflowState
    {
        // Logic to move to the next step
    }

    public function isComplete(): bool
    {
        return false;
    }
}