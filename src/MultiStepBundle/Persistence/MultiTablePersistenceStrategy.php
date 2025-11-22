<?php

namespace App\MultiStepBundle\Persistence;

use App\MultiStepBundle\Default\PersistenceStrategyInterface;
use App\MultiStepBundle\Entity\StepData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MultiTablePersistenceStrategy extends PersistanceStrategy
{
    private EntityManagerInterface $entityManager;
    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        parent::__construct($requestStack);
        $this->entityManager = $entityManager;
    }

    public function saveData(string $stepId, array $data): array
    {
        $entity = $this->entityManager->getRepository(StepData::class)->find($stepId);
        if (!$entity) {
            $entity = new StepData();
            $entity->setPersistanceType('multi_table');
            $entity->setStepNumber($stepId);
        }

        $entity->setData($data);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $data['step_id'] = $entity->getStepId();

        return $data;
    }

    public function loadData(string $stepId): array
    {
        $entity = $this->entityManager->getRepository(StepData::class)->find($stepId);
        return $entity ? $entity->getData() : [];
    }
}
