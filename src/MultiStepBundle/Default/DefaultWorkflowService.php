<?php

namespace App\MultiStepBundle\Default;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use LogicException;

abstract class DefaultWorkflowService
{
    protected DefaultSessionStateManager $sessionManager;
    protected iterable $steps;

    public function __construct(DefaultSessionStateManager $sessionManager, iterable $steps)
    {
        $this->sessionManager = $sessionManager;
        $this->steps = $steps;
    }

    public function getCurrentStep(): DefaultStepInterface
    {
        $currentStepId = $this->sessionManager->getCurrentStepId();
        foreach ($this->steps as $step) {
            if ($step->getId() === $currentStepId) {
                return $step;
            }
        }

        throw new LogicException('Invalid step ID.');
    }

    public function advance(): void
    {
        $this->sessionManager->advanceStep();
    }

    public function reset(): void
    {
        $this->sessionManager->reset();
    }
}