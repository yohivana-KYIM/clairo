<?php

namespace App\AdminBundle\SubBundles\SortingBundle\Infrastructure\Config;

class SortingConfig
{
    private string $sortingStrategy;

    public function __construct(array $config = [])
    {
        $this->sortingStrategy = $config['sorting_strategy'] ?? 'single'; // Default: single
    }

    public function getSortingStrategy(): string
    {
        return $this->sortingStrategy;
    }
}
