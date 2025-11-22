<?php

namespace App\AdminBundle\SubBundles\SearchFilterBundle\Infrastructure\Config;

class SearchFilterConfig
{
    private string $searchStrategy;
    private string $encoding;
    private bool $ignoreCase;
    private bool $exactMatch;
    private array $filters;

    public function __construct(array $config)
    {
        $this->searchStrategy = $config['search']['strategy'] ?? 'global';
        $this->encoding = $config['search']['encoding'] ?? 'utf-8';
        $this->ignoreCase = $config['search']['ignore_case'] ?? true;
        $this->exactMatch = $config['search']['exact_match'] ?? false;
        $this->filters = $config['filters'] ?? [];
    }

    public function getSearchStrategy(): string
    {
        return $this->searchStrategy;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function ignoreCase(): bool
    {
        return $this->ignoreCase;
    }

    public function exactMatch(): bool
    {
        return $this->exactMatch;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }
}
