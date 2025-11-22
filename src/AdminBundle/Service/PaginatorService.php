<?php

namespace App\AdminBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorService
{
    public function paginate(QueryBuilder $qb, int $page, int $limit = 10): array
    {
        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $limit);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        return [
            'items' => $paginator->getIterator(),
            'totalItems' => $totalItems,
            'pagesCount' => $pagesCount,
        ];
    }
}