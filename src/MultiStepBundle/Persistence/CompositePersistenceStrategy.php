<?php

namespace App\MultiStepBundle\Persistence;

use App\MultiStepBundle\Default\PersistenceStrategyInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CompositePersistenceStrategy implements PersistenceStrategyInterface
{
    private array $strategies;
    private array $data = [];

    private string $currentStepSessionKey;
    private string $dataSessionKey;
    private SessionInterface $session;

    private string $defaultStepPrefix = '';

    public function __construct(
        SessionPersistenceStrategy              $sessionStrategy,
        SingleTablePersistenceStrategy          $singleTableStrategy,
        MultiTablePersistenceStrategy           $multiTableStrategy,
        DualSessionReadSingleTableWriteStrategy $dualSessionReadSingleTableWriteStrategy,
        PersonMicroCesamePersistanceStrategy    $personCesamePersistanceStrategy,
        VehicleMicroCesamePersistanceStrategy    $vehicleCesamePersistanceStrategy,
        RequestStack                            $requestStack,
    ) {
        $this->strategies = [
            'session' => $sessionStrategy,
            'single_table' => $singleTableStrategy,
            'multi_table' => $multiTableStrategy,
            'dual_session_single_table' => $dualSessionReadSingleTableWriteStrategy,
            'microcesame.person' => $personCesamePersistanceStrategy,
            'microcesame.vehicle' => $vehicleCesamePersistanceStrategy,
        ];
        $this->session = $requestStack->getSession();
    }

    public function saveData(string $stepId, array $data): array
    {
        $strategy = $this->getStrategyForStep($stepId);
        $strategy->saveData($stepId, $data);
        return $data;
    }

    public function loadData(string $stepId): array
    {
        $strategy = $this->getStrategyForStep($stepId);
        return $strategy->loadData($stepId);
    }

    public function getCurrentStep(): string
    {
        return $this->session->get($this->getCurrentStepSessionKey(), sprintf('%sstep_one', $this->getDefaultStepPrefix()));
    }

    public function setCurrentStep(string $stepId): void
    {
        $this->session->set($this->getCurrentStepSessionKey(), $stepId);
    }

    private function getStrategyForStep(string $stepId): PersistanceStrategy
    {
        $stepStrategy = $this->data[$stepId]['strategy'] ?? 'session';
        if (!isset($this->strategies[$stepStrategy])) {
            throw new InvalidArgumentException("Persistence strategy '{$stepStrategy}' not found for step '{$stepId}'.");
        }

        return $this->strategies[$stepStrategy];
    }

    public function getCurrentStepSessionKey(): string
    {
        return $this->currentStepSessionKey;
    }

    public function setCurrentStepSessionKey(string $currentStepSessionKey): void
    {
        $this->currentStepSessionKey = $currentStepSessionKey;
        /** @var PersistanceStrategy $strategy */
        foreach ($this->strategies as $strategy ) {
            $strategy->setCurrentStepSessionKey($currentStepSessionKey);
        }
    }

    public function getDataSessionKey(): string
    {
        return $this->dataSessionKey;
    }

    public function setDataSessionKey(string $dataSessionKey): void
    {
        $this->dataSessionKey = $dataSessionKey;
        /** @var PersistanceStrategy $strategy */
        foreach ($this->strategies as $strategy ) {
            $strategy->setDataSessionKey($dataSessionKey);
        }
    }

    public function loadStrategyById(string $strategy): PersistanceStrategy {
        return $this->strategies[$strategy];
    }

    public function getDefaultStepPrefix(): string
    {
        return $this->defaultStepPrefix;
    }

    public function setDefaultStepPrefix(string $defaultStepPrefix): void
    {
        $this->defaultStepPrefix = $defaultStepPrefix;
    }

    public function clearAllData(): void
    {
        /** @var PersistanceStrategy $strategy */
        foreach ($this->strategies as $strategy ) {
            $strategy->clearAllData();
        }
    }
}
