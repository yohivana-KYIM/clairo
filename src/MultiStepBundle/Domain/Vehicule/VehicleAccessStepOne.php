<?php

namespace App\MultiStepBundle\Domain\Vehicule;

use App\MultiStepBundle\Form\Vehicule\VehicleAccessStepOneFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormInterface;

#[AutoconfigureTag('vehicule.multi_step.workflow_step')]
#[AsTaggedItem(index: 1)]
class VehicleAccessStepOne extends AbstractVehicleStep
{
    protected array $data = [];
    protected string $mode;

    public function getId(): string
    {
        return 'vehicle_step_one';
    }

    public function getDefaultIndex(): int
    {
        return 1;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
    }

    public function getFormType(): string
    {
        return VehicleAccessStepOneFormType::class;
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
        return 'Informations sur la demande';
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
        return 'js/multistep/vehicle_access_step_one_script.js';
    }
}