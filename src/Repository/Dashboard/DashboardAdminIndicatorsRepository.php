<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\DashboardAdminIndicators;
use Doctrine\Persistence\ManagerRegistry;

class DashboardAdminIndicatorsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardAdminIndicators::class);
    }

    public function one(): ?DashboardAdminIndicators
    {
        return $this->createQueryBuilder('v')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}