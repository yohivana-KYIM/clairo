<?php

namespace App\MultiStepBundle\Application;

use App\MultiStepBundle\Default\DefaultStepInterface;
use App\MultiStepBundle\Default\PersistenceStrategyInterface;
use App\MultiStepBundle\Domain\Person\AbstractPersonStep;
use App\MultiStepBundle\Entity\StepData;
use App\MultiStepBundle\Infrastructure\Symfony\WorkflowMethodEvent;
use DateTime;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Traversable;

class PersonAccessWorkflowService
{

    public function __construct(
        private readonly PersistenceStrategyInterface                           $persistenceStrategy,
        #[AutowireIterator('person.multi_step.workflow_step')] private iterable $steps,
        private readonly RouterInterface $router,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
        $stepArray = [];
        foreach ($this->steps as $step) {
            $stepArray[$step->getDefaultIndex()] = $step;
        }
        ksort($stepArray);
        $this->steps = $stepArray;
        $this->persistenceStrategy->setDefaultStepPrefix(AbstractPersonStep::STEP_PREFIX);
        $this->persistenceStrategy->setCurrentStepSessionKey('person.current_step');
        $this->persistenceStrategy->setDataSessionKey('person.workflow_data');
    }

    private function dispatchBefore(string $method, array $params = []): void
    {
        $this->eventDispatcher->dispatch(new WorkflowMethodEvent($method, $params), "workflow.$method.before");
    }

    private function dispatchAfter(string $method, array $params = [], mixed $result = null): void
    {
        $this->eventDispatcher->dispatch(new WorkflowMethodEvent($method, $params, $result), "workflow.$method.after");
    }

    public function getCurrentStep(): DefaultStepInterface
    {
        $method = __FUNCTION__;
        $currentStepId = $this->persistenceStrategy->getCurrentStep();
        $this->dispatchBefore($method, [
            'currentStepId' => $currentStepId,
            'steps' => $this->steps,
        ]);
        foreach ($this->steps as $step) {
            if (str_contains($step->getId(), $currentStepId)) {
                return $step;
            }
        }

        $this->dispatchAfter($method, [
            'currentStepId' => $currentStepId,
            'steps' => $this->steps
        ], $step ?? null);

        throw new RuntimeException("Invalid step ID: $currentStepId");
    }

    public function advance(): bool
    {
        $method = __FUNCTION__;
        $stepsArray = $this->getStepsArray();
        /** @var DefaultStepInterface $obj */
        $stepKeys = array_values(array_map(fn($obj) => $obj->getId(), $stepsArray));
        $currentStepId = $this->persistenceStrategy->getCurrentStep();
        $currentIndex = array_search($currentStepId, $stepKeys, true);

        $this->dispatchBefore($method, [
            'stepsArray' => $stepsArray,
            'stepKeys' => $stepKeys,
            'currentStepId' => $currentStepId,
            'currentIndex' => $currentIndex
        ]);

        $result = false;
        $nextStepId = null;

        $pas = 1;
        if (($this->getAllData()['person_step_one']['access_duration'] ?? null) === 'temporaire') {
            if (($stepKeys[$currentIndex] ?? null) === 'person_step_two') {
                $pas = 2;
            }
        }
        if ($currentIndex !== false && isset($stepKeys[$currentIndex + $pas])) {
            $nextStepId = $stepKeys[$currentIndex + $pas];
            $this->persistenceStrategy->setCurrentStep($nextStepId);
            $result = true;
        }

        $this->dispatchAfter($method, [
            'stepsArray' => $stepsArray,
            'stepKeys' => $stepKeys,
            'currentStepId' => $currentStepId,
            'currentIndex' => $currentIndex,
            'nextStepId' => $nextStepId
        ], $result);
        return $result;
    }

    public function getCurrentStepId(): string
    {
        $method = __FUNCTION__;

        $this->dispatchBefore($method);

        $step = $this->getCurrentStep();
        $stepId = $step->getId();

        $this->dispatchAfter($method, [
            'step' => $step,
        ], $stepId);

        return $stepId;
    }

    public function isComplete(): bool
    {
        $method = __FUNCTION__;

        $stepsArray = $this->getStepsArray();
        $currentStepId = $this->persistenceStrategy->getCurrentStep();
        $lastStep = end($stepsArray);
        $lastStepId = $lastStep?->getId();

        $this->dispatchBefore($method, [
            'stepsArray' => $stepsArray,
            'currentStepId' => $currentStepId,
            'lastStepId' => $lastStepId,
        ]);

        $result = $currentStepId === $lastStepId;

        $this->dispatchAfter($method, [
            'stepsArray' => $stepsArray,
            'currentStepId' => $currentStepId,
            'lastStepId' => $lastStepId,
        ], $result);

        return $result;
    }

    public function getAllData(bool $forView = false): array
    {
        $method = __FUNCTION__;

        $this->dispatchBefore($method, [
            'forView' => $forView,
        ]);

        $allData = [];

        foreach ($this->steps as $step) {
            $stepId = $step->getId();
            $stepName = $step->getName();
            $stepData = $this->persistenceStrategy->loadData($stepId);

            // Format DateTime values
            foreach ($stepData as $key => $value) {
                if ($value instanceof DateTime) {
                    $stepData[$key] = $value->format('d/m/Y');
                }
            }

            $allData[$stepName] = $stepData;
        }

        if ($forView) {
            $result = $allData;
        } else {
            $result = $this->cleanMultistepData($allData);
        }

        $this->dispatchAfter($method, [
            'forView' => $forView,
            'rawData' => $allData,
        ], $result);

        return $result;
    }

    public function generateStepTrail(): string
    {
        $method = __FUNCTION__;

        $stepsArray = $this->steps instanceof Traversable ? iterator_to_array($this->steps) : $this->steps;
        $currentStepId = $this->getCurrentStepId();

        $this->dispatchBefore($method, [
            'stepsArray' => $stepsArray,
            'currentStepId' => $currentStepId,
        ]);

        $trail = [];

        /** @var DefaultStepInterface $step */
        foreach ($stepsArray as $step) {
            $index = $step->getDefaultIndex();
            $name = $step->getName();
            if ($step->getId() === $currentStepId) {
                $trail[$index] = '<strong>' . $name . '</strong>';
            } else {
                $trail[$index] = $name;
            }
        }

        ksort($trail);
        $result = implode(' > ', $trail);

        $this->dispatchAfter($method, [
            'stepsArray' => $stepsArray,
            'currentStepId' => $currentStepId,
            'trailArray' => $trail,
        ], $result);

        return $result;
    }

    public function goBack(): void
    {
        $method = __FUNCTION__;

        $stepsArray = $this->getStepsArray();
        $stepKeys = array_values(array_map(fn($obj) => $obj->getId(), $stepsArray));
        $currentStepId = $this->persistenceStrategy->getCurrentStep();
        $currentIndex = array_search($currentStepId, $stepKeys, true);
        $pas = 1;
        if (($this->getAllData()['person_step_one']['access_duration'] ?? null) === 'temporaire') {
            if (($stepKeys[$currentIndex] ?? null) === 'person_step_five') {
                $pas = 2;
            }
        }
        $previousStepId = $stepKeys[$currentIndex - $pas] ?? null;

        $this->dispatchBefore($method, [
            'stepsArray' => $stepsArray,
            'stepKeys' => $stepKeys,
            'currentStepId' => $currentStepId,
            'currentIndex' => $currentIndex,
            'previousStepId' => $previousStepId,
        ]);

        if ($currentIndex !== false && isset($stepKeys[$currentIndex - $pas])) {
            $this->persistenceStrategy->setCurrentStep($previousStepId);
            $this->dispatchAfter($method, [
                'stepsArray' => $stepsArray,
                'stepKeys' => $stepKeys,
                'currentStepId' => $currentStepId,
                'currentIndex' => $currentIndex,
                'previousStepId' => $previousStepId,
            ]);
        } else {
            $this->dispatchAfter($method, [
                'stepsArray' => $stepsArray,
                'stepKeys' => $stepKeys,
                'currentStepId' => $currentStepId,
                'currentIndex' => $currentIndex,
                'previousStepId' => $previousStepId,
            ]);
            throw new RuntimeException("Invalid step ID: $currentStepId");
        }
    }

    public function saveData(array $data, ?string $mode = null, ?string $customStepId = null): void
    {
        $method = __FUNCTION__;

        $currentStep = $this->getCurrentStep();
        $stepId = $customStepId ?? $currentStep->getId();

        $params = [
            'data' => $data,
            'mode' => $mode,
            'customStepId' => $customStepId,
            'currentStep' => $currentStep,
            'stepId' => $stepId,
        ];

        $this->dispatchBefore($method, $params);

        $result = null;

        if ($mode !== 'microcesame.person') {
            $this->persistenceStrategy->saveData($stepId, $data);
        }

        if ($mode) {
            $persistenceStrategy = $this->persistenceStrategy->loadStrategyById($mode);
            $datas = $this->getAllData();
            if ($data) {
                $datas[$currentStep->getId()] = $data;
            }
            $result = $persistenceStrategy->saveData($stepId, $datas);
        }

        $this->dispatchAfter($method, array_merge($params, [
            'datas' => $datas ?? null,
            'externalPersistenceStrategy' => $persistenceStrategy ?? null,
        ]), $result);
    }

    public function updateStepDatas(array $datas, ?string $mode = null): void
    {
        $method = __FUNCTION__;

        $persistenceStrategy = $this->persistenceStrategy;
        if ($mode) {
            $persistenceStrategy = $this->persistenceStrategy->loadStrategyById($mode);
        }

        $results = [];

        $this->dispatchBefore($method, [
            'datas' => $datas,
            'mode' => $mode,
            'resolvedPersistenceStrategy' => $persistenceStrategy,
        ]);

        foreach ($this->steps as $currentStep) {
            $stepId = $currentStep->getId();
            $dataForStep = $datas[$stepId] ?? [];

            $result = $persistenceStrategy->saveData($stepId, $dataForStep);
            $results[$stepId] = $result;

            if (array_key_exists('step_id', $result ?? []) && $mode) {
                $this->persistenceStrategy->saveData($stepId, $result);
            }
        }

        $this->dispatchAfter($method, [
            'datas' => $datas,
            'mode' => $mode,
            'resolvedPersistenceStrategy' => $persistenceStrategy,
        ], $results);
    }

    public function loadData(): array
    {
        $method = __FUNCTION__;

        $currentStep = $this->getCurrentStep();
        $stepId = $currentStep->getId();

        $this->dispatchBefore($method, [
            'currentStep' => $currentStep,
            'stepId' => $stepId,
        ]);

        $data = $this->persistenceStrategy->loadData($stepId);

        $this->dispatchAfter($method, [
            'currentStep' => $currentStep,
            'stepId' => $stepId,
        ], $data);

        return $data;
    }

    public function generateButtons(?StepData $stepData = null): array
    {
        $method = __FUNCTION__;

        $routeParams = [];
        if ($stepData !== null) {
            $routeParams['id'] = $stepData->getStepId();
        }

        $this->dispatchBefore($method, [
            'stepData' => $stepData,
            'routeParams' => $routeParams,
        ]);

        $buttons = [];

        if (!$this->isFirstStep()) {
            $buttons['previous'] = [
                'label' => 'Retour',
                'route' => $this->router->generate('person_access_previous', $routeParams),
                'class' => 'btn-previous',
                'action' => 'previous',
                'attr' => 'formnovalidate',
                'type' => 'button'
            ];
        }

        $buttons['persist'] = [
            'label' => 'Enregistrer',
            'route' => $this->router->generate('person_access_persist', $routeParams),
            'class' => 'btn-persist',
            'action' => 'persist',
            'type' => 'button',
        ];

        $buttons['next'] = [
            'label' => 'Suivant',
            'route' => $this->router->generate('person_access_request', $routeParams),
            'class' => 'btn-next',
            'action' => 'next',
            'type' => 'button'
        ];

        $this->dispatchAfter($method, [
            'stepData' => $stepData,
            'routeParams' => $routeParams,
        ], $buttons);

        return $buttons;
    }

    public function isFirstStep(): bool
    {
        $method = __FUNCTION__;

        $stepsArray = $this->getStepsArray();
        $currentStepId = $this->persistenceStrategy->getCurrentStep();
        $firstStep = current($stepsArray);
        $firstStepId = $firstStep?->getId();

        $this->dispatchBefore($method, [
            'stepsArray' => $stepsArray,
            'currentStepId' => $currentStepId,
            'firstStepId' => $firstStepId,
        ]);

        $result = $currentStepId === $firstStepId;

        $this->dispatchAfter($method, [
            'stepsArray' => $stepsArray,
            'currentStepId' => $currentStepId,
            'firstStepId' => $firstStepId,
        ], $result);

        return $result;
    }

    private function getStepsArray(): array
    {
        if (empty($this->steps)) return [];
        if ($this->steps instanceof Traversable) {
            return iterator_to_array($this->steps);
        } else {
            return (array)$this->steps;
        }
    }

    public function resetWorkflow(): void
    {
        $method = __FUNCTION__;

        $stepsArray = $this->getStepsArray();
        $firstStep = reset($stepsArray);
        $firstStepId = $firstStep instanceof DefaultStepInterface ? $firstStep->getId() : null;

        $this->dispatchBefore($method, [
            'stepsArray' => $stepsArray,
            'firstStep' => $firstStep,
            'firstStepId' => $firstStepId,
        ]);

        $this->persistenceStrategy->clearAllData();

        if ($firstStep instanceof DefaultStepInterface) {
            $this->persistenceStrategy->setCurrentStep($firstStepId);
        }

        $this->dispatchAfter($method, [
            'stepsArray' => $stepsArray,
            'firstStep' => $firstStep,
            'firstStepId' => $firstStepId,
        ]);
    }

    public function setCurrentStep(string $stepId): void
    {
        $method = __FUNCTION__;

        $this->dispatchBefore($method, [
            'stepId' => $stepId,
        ]);

        $this->persistenceStrategy->setCurrentStep($stepId);

        $this->dispatchAfter($method, [
            'stepId' => $stepId,
        ]);
    }

    public function cleanMultistepData(array $rawData): array
    {
        $method = __FUNCTION__;

        $this->dispatchBefore($method, [
            'rawData' => $rawData,
        ]);

        // Step 1: Map names to step IDs
        $stepNamesId = [];
        foreach ($this->steps as $step) {
            $stepNamesId[$step->getName()] = $step->getId();
        }

        // Step 2: Count global keys
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
        unset($data);

        // Step 3: Identify global keys
        $globalKeys = [];
        foreach ($keyCounts as $k => $count) {
            if ($count > 1) {
                $globalKeys[] = $k;
            }
        }

        // Step 4: No global keys → map names to IDs
        if (empty($globalKeys)) {
            foreach ($rawData as $stepName => $data) {
                if (isset($data['step_id'])) {
                    $rawData['step_id'] = $data['step_id'];
                    unset($data['step_id']);
                }

                if (isset($stepNamesId[$stepName])) {
                    $rawData[$stepNamesId[$stepName]] = $data;
                    unset($rawData[$stepName]);
                }
            }

            $this->dispatchAfter($method, [
                'stepNamesId' => $stepNamesId,
                'keyCounts' => $keyCounts,
                'globalKeys' => [],
            ], $rawData);

            return $rawData;
        }

        // Step 5: Handle global keys and normalize data
        foreach ($rawData as $sectionName => &$sectionData) {
            if (!is_array($sectionData)) continue;

            foreach ($globalKeys as $globalKey) {
                if (isset($sectionData[$globalKey])) {
                    $rawData[$globalKey] = $sectionData[$globalKey];
                    unset($sectionData[$globalKey]);
                }
            }
        }
        unset($sectionData);

        $this->dispatchAfter($method, [
            'stepNamesId' => $stepNamesId,
            'keyCounts' => $keyCounts,
            'globalKeys' => $globalKeys,
        ], $rawData);

        return $rawData;
    }

    public function stepNameExistsForUser(string $mode, string $stepId): bool
    {
        $persistenceStrategy = $this->persistenceStrategy->loadStrategyById($mode);

        return $persistenceStrategy->stepNameExistsForUser($stepId);
    }
}
