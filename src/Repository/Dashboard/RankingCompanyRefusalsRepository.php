<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RankingCompanyRefusals;
use Doctrine\Persistence\ManagerRegistry;

class RankingCompanyRefusalsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RankingCompanyRefusals::class);
    }

    public function top(int $limit = 20): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.refusals', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }
}