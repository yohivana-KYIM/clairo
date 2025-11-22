<?php

namespace App\MultiStepBundle\Persistence;

use App\MultiStepBundle\Entity\StepData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DualSessionReadSingleTableWriteStrategy extends PersistanceStrategy
{
    public function __construct(private readonly RequestStack $requestStack, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($this->requestStack);
    }

    public function saveData(string $stepId, array $data): array
    {
        // Save to session for faster reads
        $sessionData = $this->session->get($this->getDataSessionKey(), []);
        $sessionData[$stepId] = $data;


        // Write to single database table for persistence
        $entity = $this->entityManager->getRepository(StepData::class)->find($stepId);
        if (!$entity) {
            $entity = new StepData();
            $entity->setStepNumber($stepId);
        }

        $entity->setData($data);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $this->session->set($this->getDataSessionKey(), NULL);
        $data['step_id'] = $entity->getStepId();

        return $data;
    }

    public function loadData(string $stepId): array
    {
        // Try to load from session
        $sessionData = $this->session->get($this->getDataSessionKey(), []);
        if (isset($sessionData[$stepId])) {
            return $sessionData[$stepId];
        }
        return [];
    }
}
