<?php

namespace App\MultiStepBundle\Default;

use App\MultiStepBundle\Persistence\PersistanceStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

interface PersistenceStrategyInterface
{
    public function saveData(string $stepId, array $data): array;

    public function loadData(string $stepId): array;

    public function getCurrentStep(): string;

    public function setCurrentStep(string $stepId): void;

    public function setCurrentStepSessionKey(string $currentStepSessionKey): void;
    public function getCurrentStepSessionKey(): string;

    public function loadStrategyById(string $strategy): PersistanceStrategy;
    public function getDefaultStepPrefix(): string;
    public function setDefaultStepPrefix(string $defaultStepPrefix): void;
    public function clearAllData(): void;
}
