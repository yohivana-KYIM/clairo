<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RankingCompanyMissingDocs;
use Doctrine\Persistence\ManagerRegistry;

class RankingCompanyMissingDocsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RankingCompanyMissingDocs::class);
    }

    public function top(int $limit = 20): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.incompleteRequests', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }
}