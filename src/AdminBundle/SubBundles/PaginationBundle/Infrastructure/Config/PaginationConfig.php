<?php

namespace App\AdminBundle\SubBundles\PaginationBundle\Infrastructure\Config;

class PaginationConfig
{
    private string $paginationStrategy;
    private int $itemsPerPage;

    public function __construct(array $config)
    {
        $this->paginationStrategy = $config['strategy'] ?? 'server';
        $this->itemsPerPage = $config['items_per_page'] ?? 10;
    }

    public function getPaginationStrategy(): string
    {
        return $this->paginationStrategy;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
}
