<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\DashboardRefsecuIndicators;
use Doctrine\Persistence\ManagerRegistry;

class DashboardRefsecuIndicatorsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardRefsecuIndicators::class);
    }

    public function one(): ?DashboardRefsecuIndicators
    {
        return $this->createQueryBuilder('v')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}