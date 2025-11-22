<?php

namespace App\MultiStepBundle\Default;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DefaultSessionStateManager
{
    private const CURRENT_STEP_KEY = 'workflow_current_step';
    private SessionInterface $session;

    public function __construct(private readonly RequestStack $requestStack)
    {
        $this->session = $this->requestStack->getSession();
    }

    public function getCurrentStepId(): ?string
    {
        return $this->session->get(self::CURRENT_STEP_KEY, 'step_one');
    }

    public function advanceStep(): void
    {
        $currentStepId = $this->getCurrentStepId();
        $steps = array_keys($this->session->get(self::CURRENT_STEP_KEY . '_steps', []));
        $currentIndex = array_search($currentStepId, $steps, true);

        if ($currentIndex !== false && isset($steps[$currentIndex + 1])) {
            $this->session->set(self::CURRENT_STEP_KEY, $steps[$currentIndex + 1]);
        }
    }

    public function reset(): void
    {
        $this->session->remove(self::CURRENT_STEP_KEY);
    }
}