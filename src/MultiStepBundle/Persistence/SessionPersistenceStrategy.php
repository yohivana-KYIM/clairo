<?php

namespace App\MultiStepBundle\Persistence;

use Symfony\Component\HttpFoundation\RequestStack;

class SessionPersistenceStrategy extends PersistanceStrategy
{

    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack);
    }

    public function saveData(string $stepId, array $data): array
    {
        $sessionData = $this->session->get($this->getDataSessionKey(), []);
        $sessionData[$stepId] = $data;
        $this->session->set($this->getDataSessionKey(), $sessionData);

        return $data;
    }

    public function loadData(string $stepId): array
    {
        $sessionData = $this->session->get($this->getDataSessionKey(), []);
        return $sessionData[$stepId] ?? [];
    }
}
