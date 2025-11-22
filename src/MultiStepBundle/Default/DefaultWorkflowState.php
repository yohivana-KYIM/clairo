<?php

namespace App\MultiStepBundle\Default;

abstract class DefaultWorkflowState
{
    abstract public function getCurrentStep(): DefaultStepInterface;
    abstract public function nextStep(): DefaultWorkflowState;
    abstract public function isComplete(): bool;
}