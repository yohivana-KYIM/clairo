<?php

namespace App\MultiStepBundle\Domain\Vehicule;

use App\MultiStepBundle\Default\DefaultStepInterface;
use App\MultiStepBundle\Form\Vehicule\VehicleAccessStepThreeFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormInterface;

#[AutoconfigureTag('vehicule.multi_step.workflow_step')]
#[AsTaggedItem(index: 3)]
class VehicleAccessStepThree extends AbstractVehicleStep
{
    protected array $data = [];
    protected string $mode;

    public function getId(): string
    {
        return 'vehicle_step_three';
    }

    public function getDefaultIndex(): int
    {
        return 3;
    }

    public function getFormType(): string
    {
        return VehicleAccessStepThreeFormType::class;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function validate(FormInterface $form): bool
    {
        return $form->isValid();
    }

    public function process(FormInterface $form): void
    {
        $this->data = $form->getData();
    }

    public function isCompleted(): bool
    {
        return !empty($this->data);
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getName(): string
    {
        return 'Accès souhaité';
    }

    public function getPersistenceStrategy(): string
    {
        return 'session';
    }

    public function processLoadedData(array $data): array
    {
        return $data;
    }

    public function getCustomScriptUrl(): string
    {
        return 'js/multistep/vehicle_access_step_three_script.js';
    }
}