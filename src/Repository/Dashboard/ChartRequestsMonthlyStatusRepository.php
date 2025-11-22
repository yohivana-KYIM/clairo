<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\ChartRequestsMonthlyStatus;
use Doctrine\Persistence\ManagerRegistry;

class ChartRequestsMonthlyStatusRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartRequestsMonthlyStatus::class);
    }

    public function findByMonthRange(string $fromMonth, string $toMonth): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.month >= :from AND v.month <= :to')
            ->setParameter('from', $fromMonth)
            ->setParameter('to', $toMonth)
            ->orderBy('v.month', 'ASC')
            ->getQuery()->getResult();
    }
}