<?php

namespace App\MultiStepBundle\Domain\Vehicule;

use App\MultiStepBundle\Default\DefaultStepInterface;
use App\MultiStepBundle\Form\Vehicule\VehicleAccessStepTwoFormType;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormInterface;

#[AutoconfigureTag('vehicule.multi_step.workflow_step')]
#[AsTaggedItem(index: 2)]
class VehicleAccessStepTwo extends AbstractVehicleStep
{
    protected array $data = [];

    public function getId(): string
    {
        return 'vehicle_step_two';
    }

    public function getDefaultIndex(): int
    {
        return 2;
    }

    public function getFormType(): string
    {
        return VehicleAccessStepTwoFormType::class;
    }

    public function setMode(string $mode): void
    {
        
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
        return 'Informations sur le véhicule';
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
        return 'js/multistep/vehicle_access_step_two_script.js';
    }
}