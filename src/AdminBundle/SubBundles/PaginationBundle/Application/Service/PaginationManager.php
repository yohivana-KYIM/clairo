<?php

namespace App\AdminBundle\SubBundles\PaginationBundle\Application\Service;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\QueryBuilder;

class PaginationManager
{
    private string $strategy;
    private int $itemsPerPage;

    public function __construct(string $strategy = 'server', int $itemsPerPage = 20)
    {
        $this->strategy = $strategy;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function paginate(QueryBuilder $queryBuilder, int $page): array
    {
        if ($this->strategy === 'client') {
            return [
                'items' => $queryBuilder->getQuery()->getResult(),
                'totalItems' => count($queryBuilder->getQuery()->getResult()),
                'pagesCount' => 1,
                'currentPage' => 1,
            ];
        }

        $queryBuilder->setFirstResult(($page - 1) * $this->itemsPerPage)
            ->setMaxResults($this->itemsPerPage);

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $this->itemsPerPage);

        return [
            'items' => $paginator->getIterator(),
            'totalItems' => $totalItems,
            'pagesCount' => $pagesCount,
            'currentPage' => $page,
        ];
    }
}

