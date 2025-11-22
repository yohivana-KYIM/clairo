<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\DashboardUserIndicators;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class DashboardUserIndicatorsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DashboardUserIndicators::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function one(): ?DashboardUserIndicators
    {
        return $this->createQueryBuilder('v')->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }
}