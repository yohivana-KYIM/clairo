<?php

namespace App\MultiStepBundle\Default;

use Symfony\Component\Form\FormInterface;

interface DefaultStepInterface
{
    public function getId(): string;
    public function getName(): string;
    public function getFormType(): string;
    public function setMode(string $mode): void;
    public function setData(array $data): void;
    public function validate(FormInterface $form): bool;
    public function process(FormInterface $form): void;
    public function isCompleted(): bool;

    public function getData();
    public function getDefaultIndex(): int;
    public function getPersistenceStrategy(): string;
    public function processLoadedData(array $data): array;
}