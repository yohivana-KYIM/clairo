<?php

namespace App\MultiStepBundle\Application;

use App\MultiStepBundle\Default\DefaultStepInterface;
use App\MultiStepBundle\Default\PersistenceStrategyInterface;
use App\MultiStepBundle\Domain\Vehicule\AbstractVehicleStep;
use DateTime;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Traversable;

class VehicleAccessWorkflowService
{
    public function __construct(
        private readonly PersistenceStrategyInterface                           $persistenceStrategy,
        #[AutowireIterator('vehicule.multi_step.workflow_step')] private iterable $steps
    ) {
        $stepArray = [];
        foreach ($this->steps as $step) {
            $stepArray[$step->getDefaultIndex()] = $step;
        }
        ksort($stepArray);
        $this->steps = $stepArray;
        // mirror Person service defaults
        $this->persistenceStrategy->setDefaultStepPrefix(AbstractVehicleStep::STEP_PREFIX);
        $this->persistenceStrategy->setCurrentStepSessionKey('vehicle.current_step');
        $this->persistenceStrategy->setDataSessionKey('vehicle.workflow_data');
    }

    public function getCurrentStep(): DefaultStepInterface
    {
        $currentStepId = $this->persistenceStrategy->getCurrentStep();
        foreach ($this->steps as $step) {
            // allow partial matches if using prefixed IDs
            if (str_contains($step->getId(), $currentStepId)) {
                return $step;
            }
        }

        throw new RuntimeException("Invalid step ID: $currentStepId");
    }

    public function advance(): void
    {
        $stepsArray  = $this->getStepsArray();
        $stepKeys    = array_values(array_map(fn($obj) => $obj->getId(), $stepsArray));
        $currentStep = $this->persistenceStrategy->getCurrentStep();
        $idx         = array_search($currentStep, $stepKeys, true);

        if ($idx !== false && isset($stepKeys[$idx + 1])) {
            $this->persistenceStrategy->setCurrentStep($stepKeys[$idx + 1]);
        }
    }

    public function getCurrentStepId(): string
    {
        return $this->getCurrentStep()->getId();
    }

    public function isComplete(): bool
    {
        $stepsArray  = $this->getStepsArray();
        $lastStepId  = end($stepsArray)->getId();
        return $this->persistenceStrategy->getCurrentStep() === $lastStepId;
    }

    /**
     * @param bool $forView If true, returns grouped by step name; if false, returns flat id=>data array
     */
    public function getAllData(bool $forView = false): array
    {
        $allData = [];
        foreach ($this->steps as $step) {
            $raw = $this->persistenceStrategy->loadData($step->getId());
            foreach ($raw as $k => $v) {
                if ($v instanceof DateTime) {
                    $raw[$k] = $v->format('d/m/Y');
                }
            }
            $allData[$step->getName()] = $raw;
        }

        if ($forView) {
            return $allData;
        }

        return $this->cleanMultistepData($allData);
    }

    public function generateStepTrail(): string
    {
        $stepsArray     = $this->getStepsArray();
        $currentStepId  = $this->getCurrentStepId();
        $trail          = [];

        foreach ($stepsArray as $step) {
            $label = $step->getName();
            if ($step->getId() === $currentStepId) {
                $label = '<strong>' . $label . '</strong>';
            }
            $trail[$step->getDefaultIndex()] = $label;
        }
        ksort($trail);

        return implode(' > ', $trail);
    }

    public function goBack(): void
    {
        $stepsArray  = $this->getStepsArray();
        $stepKeys    = array_values(array_map(fn($obj) => $obj->getId(), $stepsArray));
        $currentStep = $this->persistenceStrategy->getCurrentStep();
        $idx         = array_search($currentStep, $stepKeys, true);

        if ($idx !== false && isset($stepKeys[$idx - 1])) {
            $this->persistenceStrategy->setCurrentStep($stepKeys[$idx - 1]);
        } else {
            throw new RuntimeException("Invalid step ID: $currentStep");
        }
    }

    public function saveData(array $data, ?string $mode = null, ?string $customStepId = null): void
    {
        $currentStep = $this->getCurrentStep();
        $stepId = $customStepId ?? $currentStep->getId();
        if ($mode != 'microcesame.vehicle') {
            $this->persistenceStrategy->saveData($stepId, $data);
        }
        if ($mode) {
            $persistenceStrategy = $this->persistenceStrategy->loadStrategyById($mode);
            $datas = $this->getAllData();
            if ($data) $datas[$currentStep->getId()] = $data;
            $persistenceStrategy->saveData($stepId, $datas);
        }
    }

    public function stepNameExistsForUser(string $mode, string $stepId): bool
    {
        $persistenceStrategy = $this->persistenceStrategy->loadStrategyById($mode);

        return $persistenceStrategy->stepNameExistsForUser($stepId);
    }

    public function updateStepDatas(array $datas, ?string $mode = null): void
    {
        $strategy = $mode ? $this->persistenceStrategy->loadStrategyById($mode) : $this->persistenceStrategy;
        foreach ($this->steps as $step) {
            $strategy->saveData($step->getId(), $datas[$step->getId()] ?? []);
        }
    }

    public function loadData(): array
    {
        return $this->persistenceStrategy->loadData($this->getCurrentStepId());
    }

    public function generateButtons(): array
    {
        $buttons = [];
        if (!$this->isFirstStep()) {
            $buttons['previous'] = ['label'=>'Retour','route'=>'vehicle-access-previous','class'=>'btn-previous','action'=>'previous', 'type' => 'button'];
        }
        $buttons['persist'] = ['label'=>'Enregistrer','route'=>'vehicle-access-persist','class'=>'btn-persist','action'=>'persist', 'type' => 'button'];
        $buttons['next']    = ['label'=>'Suivant','route'=>'vehicle-access-request','class'=>'btn-next','action'=>'next', 'type' => 'button'];
        return $buttons;
    }

    public function isFirstStep(): bool
    {
        $first = current($this->getStepsArray());
        return $this->persistenceStrategy->getCurrentStep() === $first->getId();
    }

    public function resetWorkflow(): void
    {
        $this->persistenceStrategy->clearAllData();
        $array = $this->getStepsArray();
        $first = reset($array);
        $this->persistenceStrategy->setCurrentStep($first->getId());
    }

    public function setCurrentStep(string $stepId): void
    {
        $this->persistenceStrategy->setCurrentStep($stepId);
    }

    public function cleanMultistepData(array $rawData): array
    {
        // Étape 1 : Mapping nom → ID
        $stepNamesId = [];
        foreach ($this->steps as $step) {
            $stepNamesId[$step->getName()] = $step->getId();
        }

        // Étape 2 : Compter les clés globales
        $keyCounts = [];
        foreach ($rawData as $key => &$data) {
            if (!is_array($data)) continue;

            $keyCounts[$key] = ($keyCounts[$key] ?? 0) + 1;

            foreach ($data as $subKey => $subValue) {
                if (is_array($subValue)) {
                    $keyCounts[$subKey] = ($keyCounts[$subKey] ?? 0) + 1;
                }
            }
        }
        unset($data); // Nettoyer la référence

        // Étape 3 : Identifier les clés globales
        $globalKeys = [];
        foreach ($keyCounts as $k => $count) {
            if ($count > 1) {
                $globalKeys[] = $k;
            }
        }

        // Étape 4 : Si aucune clé globale, on mappe simplement les noms
        if (empty($globalKeys)) {
            foreach ($rawData as $stepName => $data) {

                // Extraire "step_id" s’il existe et le placer à la racine
                if (isset($data['step_id'])) {
                    $rawData['step_id'] = $data['step_id'];
                    unset($data['step_id']);
                }

                if (isset($stepNamesId[$stepName])) {
                    $rawData[$stepNamesId[$stepName]] = $data;
                    unset($rawData[$stepName]);
                }
            }

            return $rawData;
        }

        // Étape 5 : Sinon, nettoyer les clés globales et déplacer les step_id
        foreach ($rawData as $sectionName => &$sectionData) {
            if (!is_array($sectionData)) continue;

            // Nettoyage des clés globales
            foreach ($globalKeys as $globalKey) {
                if (isset($sectionData[$globalKey])) {
                    $rawData[$globalKey] = $sectionData[$globalKey];
                    unset($sectionData[$globalKey]);
                }
            }
        }
        unset($sectionData);

        return $rawData;
    }

    private function getStepsArray(): array
    {
        if ($this->steps instanceof Traversable) {
            return iterator_to_array($this->steps);
        }
        return (array)$this->steps;
    }
}
