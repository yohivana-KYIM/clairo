<?php

namespace App\AdminBundle\SubBundles\SortingBundle\Application\Service;

use App\AdminBundle\SubBundles\SortingBundle\Infrastructure\Config\SortingConfig;

class SortManager
{
    private string $sortingStrategy;

    public function __construct(SortingConfig $config)
    {
        $this->sortingStrategy = $config->getSortingStrategy();
    }

    public function processSorting(array $currentSorts, string $newColumn): array
    {
        // Determine the next state for the clicked column
        $nextState = match ($currentSorts[$newColumn] ?? '') {
            '' => 'ASC',
            'ASC' => 'DESC',
            'DESC' => '',
        };

        if ($this->sortingStrategy === 'single') {
            return $nextState ? [$newColumn => $nextState] : [];
        }

        if ($nextState) {
            $currentSorts[$newColumn] = $nextState;
        } else {
            unset($currentSorts[$newColumn]);
        }

        return $currentSorts;
    }
}
