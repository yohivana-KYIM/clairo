<?php

namespace App\AdminBundle\SubBundles\SearchFilterBundle\Application\Service;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SearchFilterManager
{
    private string $strategy;
    private string $encoding;
    private bool $ignoreCase;
    private bool $exactMatch;
    private array $filters;
    private array $searchableClasses;
    private array $filterableClasses;

    public function __construct(ParameterBagInterface $params, string $strategy = 'global', string $encoding = 'utf-8', bool $ignoreCase = true, bool $exactMatch = false, array $filters = [])
    {
        $this->strategy = $strategy;
        $this->encoding = $encoding;
        $this->ignoreCase = $ignoreCase;
        $this->exactMatch = $exactMatch;
        $this->filters = $filters;
        $this->searchableClasses = $params->get('search_filter_bundle.searchable_classes');
        $this->filterableClasses = $params->get('search_filter_bundle.filterable_classes');
    }

    public function encodeSearchTerm(string $searchTerm): string
    {
        return match ($this->encoding) {
            'base64' => base64_encode($searchTerm),
            'url' => urlencode($searchTerm),
            default => $searchTerm,
        };
    }

    public function applySearch(QueryBuilder $queryBuilder, string $entityClass, string $searchTerm, array $columns): void
    {
        if (!$searchTerm) return;

        // If the entity has a custom search method, use it
        if (isset($this->searchableClasses[$entityClass])) {
            $customMethods = $this->searchableClasses[$entityClass];
            foreach ($customMethods as $method) {
                if (method_exists($entityClass, $method)) {
                    $entityClass::$method($queryBuilder, $searchTerm);
                    return;
                }
            }
        }

        $encodedSearchTerm = $this->encodeSearchTerm($searchTerm);
        $searchClause = [];

        foreach ($columns as $column) {
            $condition = $this->exactMatch ? "e.$column = :search" : "e.$column LIKE :search";
            if ($this->ignoreCase) {
                $condition = "LOWER(e.$column) " . ($this->exactMatch ? "= LOWER(:search)" : "LIKE LOWER(:search)");
            }
            $searchClause[] = $condition;
        }

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(...$searchClause)
        )->setParameter('search', $this->exactMatch ? $encodedSearchTerm : "%$encodedSearchTerm%");
    }

    public function applyFilters(QueryBuilder $queryBuilder, string $entityClass, array $filters): void
    {
        foreach ($filters as $key => $value) {

            if (isset($this->filterableClasses[$entityClass])) {
                foreach ($this->filterableClasses[$entityClass] as $method) {
                    if (method_exists($entityClass, $method)) {
                        $entityClass::$method($queryBuilder, $filters);
                        return;
                    }
                }
            }

            if (isset($this->filters[$key]) && $this->filters[$key]) {
                if (is_array($value)) {
                    $queryBuilder->andWhere("e.$key IN (:".$key.")")->setParameter($key, $value);
                } elseif (is_bool($value)) {
                    $queryBuilder->andWhere("e.$key = :".$key)->setParameter($key, (int) $value);
                } elseif ($this->filters[$key] === 'json') {
                    $queryBuilder->andWhere("JSON_CONTAINS(e.$key, :$key)")->setParameter($key, json_encode($value));
                } else {
                    $queryBuilder->andWhere("e.$key = :".$key)->setParameter($key, $value);
                }
            }
        }
    }

    public function getStrategy(): string
    {
        return $this->strategy;
    }

    public function setStrategy(string $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function getSearchableClasses(): array
    {
        return $this->searchableClasses;
    }

    public function setSearchableClasses(array $searchableClasses): void
    {
        $this->searchableClasses = $searchableClasses;
    }

    public function getFilterableClasses(): array
    {
        return $this->filterableClasses;
    }

    public function setFilterableClasses(array $filterableClasses): void
    {
        $this->filterableClasses = $filterableClasses;
    }
}
