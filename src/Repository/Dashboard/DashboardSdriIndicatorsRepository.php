<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\DashboardSdriIndicators;
use Doctrine\Persistence\ManagerRegistry;

class DashboardSdriIndicatorsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardSdriIndicators::class);
    }

    public function one(): ?DashboardSdriIndicators
    {
        return $this->createQueryBuilder('v')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}