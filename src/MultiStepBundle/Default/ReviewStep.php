<?php

namespace App\MultiStepBundle\Default;

use Symfony\Component\Form\FormInterface;
class ReviewStep implements DefaultStepInterface
{
    private array $allData;

    public function __construct(array $allData)
    {
        $this->allData = $allData;
    }

    public function getId(): string
    {
        return 'review_step';
    }

    public function getFormType(): string
    {
        return ''; // No form type for review step
    }

    public function setMode(string $mode): void
    {
        // Not applicable for review step
    }

    public function setData(array $data): void
    {
        // Not applicable for review step
    }

    public function validate(FormInterface $form): bool
    {
        return true; // No validation needed for review
    }

    public function process(FormInterface $form): void
    {
        // No processing needed for review
    }

    public function isCompleted(): bool
    {
        return true;
    }

    public function getData(): array
    {
        return $this->allData;
    }

    public function getName(): string
    {
        return 'Validation et récapitulatif des informations';
    }

    public function getDefaultIndex(): int
    {
        return 100;
    }

    public function getPersistenceStrategy(): string
    {
        return 'dual_session_single_table';
    }

    public function processLoadedData(array $data): array
    {
        return $data;
    }
}