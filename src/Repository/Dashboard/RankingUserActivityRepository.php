<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RankingUserActivity;
use Doctrine\Persistence\ManagerRegistry;

class RankingUserActivityRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RankingUserActivity::class);
    }

    public function top(int $limit = 20): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.totalSubmitted', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }
}