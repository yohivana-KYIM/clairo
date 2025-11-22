<?php

namespace App\MultiStepBundle\Persistence;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PersistanceStrategy
{
    private string $currentStepSessionKey;
    private string $dataSessionKey;
    protected SessionInterface $session;

    public function __construct(RequestStack $requestStack) {
        $this->session = $requestStack->getSession();
    }

    public function getCurrentStepSessionKey(): string
    {
        return $this->currentStepSessionKey;
    }

    public function setCurrentStepSessionKey(string $currentStepSessionKey): void
    {
        $this->currentStepSessionKey = $currentStepSessionKey;
    }

    public function getCurrentStep(): string
    {
        return $this->session->get($this->getCurrentStepSessionKey(), '');
    }

    public function setCurrentStep(string $stepId): void
    {
        $this->session->set($this->getCurrentStepSessionKey(), $stepId);
    }

    public function getDataSessionKey(): string
    {
        return $this->dataSessionKey;
    }

    public function setDataSessionKey(string $dataSessionKey): void
    {
        $this->dataSessionKey = $dataSessionKey;
    }

    public function clearAllData(): void
    {
        $this->session->remove($this->getCurrentStepSessionKey());
        $this->session->remove($this->getDataSessionKey());
    }

    public function stepNameExistsForUser(string $stepId): bool
    {
        return $this->session->has($stepId);
    }
}