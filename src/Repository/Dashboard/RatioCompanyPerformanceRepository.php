<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RatioCompanyPerformance;
use Doctrine\Persistence\ManagerRegistry;

class RatioCompanyPerformanceRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RatioCompanyPerformance::class);
    }

    public function bestApprovalRate(int $limit = 20): array
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.approvalRate', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();
    }

    public function forCompany(string $company): ?RatioCompanyPerformance
    {
        return $this->findOneBy(['companyName' => $company]);
    }
}