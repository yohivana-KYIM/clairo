<?php

namespace App\AdminBundle\Infrastructure\Config;

class AdminConfig
{
    private array $entities = [];
    private string $sortingStrategy;
    private string $renderingStrategy;

    public function __construct(array $config = [])
    {
        $this->entities = $config['entities'] ?? [];
        $this->sortingStrategy = $config['sorting_strategy'] ?? 'single'; // Default: single
        $this->renderingStrategy = $config['rendering_strategy'] ?? 'twig_bootstrap'; // Default: Twig-Bootstrap
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function getSortingStrategy(): string
    {
        return $this->sortingStrategy;
    }

    public function getRenderingStrategy(): string
    {
        return $this->renderingStrategy;
    }

    public function setRenderingStrategy(string $renderingStrategy): void
    {
        $this->renderingStrategy = $renderingStrategy;
    }
}
