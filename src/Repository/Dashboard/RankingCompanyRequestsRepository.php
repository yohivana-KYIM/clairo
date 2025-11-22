<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RankingCompanyRequests;
use Doctrine\Persistence\ManagerRegistry;

class RankingCompanyRequestsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RankingCompanyRequests::class);
    }

    public function top(int $limit = 20): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.totalRequests', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }
}