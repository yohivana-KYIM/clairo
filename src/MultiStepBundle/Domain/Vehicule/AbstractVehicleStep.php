<?php

namespace App\MultiStepBundle\Domain\Vehicule;

use App\MultiStepBundle\Default\DefaultStepInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\Form\FormInterface;

abstract class AbstractVehicleStep implements DefaultStepInterface
{
    const STEP_PREFIX = 'vehicle_';
    protected array $data = [];
    protected string $mode;
    protected array $previousFormData = [];

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

    public function getData(): array
    {
        return $this->data;
    }

    public function isCompleted(): bool
    {
        return !empty($this->data);
    }

    public function validate(FormInterface $form): bool
    {
        return $form->isValid();
    }

    public function process(FormInterface $form): void
    {
        $this->data = $form->getData();
    }

    public function processLoadedData(array $data): array
    {
        return $data;
    }

    public function getPersistenceStrategy(): string
    {
        return 'session';
    }
    public abstract function getCustomScriptUrl(): string;

    public function setPreviousFormData(array $previousFormData): void
    {
        $this->previousFormData = $previousFormData;
    }
}
