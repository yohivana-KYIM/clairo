<?php

namespace App\MultiStepBundle\Default;

use LogicException;

class CompleteState extends DefaultWorkflowState
{
    public function getCurrentStep(): DefaultStepInterface
    {
        throw new LogicException('Workflow is complete.');
    }

    public function nextStep(): DefaultWorkflowState
    {
        throw new LogicException('No more steps available.');
    }

    public function isComplete(): bool
    {
        return true;
    }
}