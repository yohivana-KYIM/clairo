<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\DeadlinesUser;
use Doctrine\Persistence\ManagerRegistry;

class DeadlinesUserRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeadlinesUser::class);
    }

    /** @return DeadlinesUser[] */
    public function forUser(string $email): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.employeeEmail = :e')
            ->setParameter('e', $email)
            ->orderBy('v.stepId', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return DeadlinesUser[] */
    public function expiringInDays(int $maxDays): array
    {
        // handle NULLs explicitly and group conditions
        return $this->createQueryBuilder('v')
            ->andWhere('(v.daysUntilContractEnd IS NOT NULL AND v.daysUntilContractEnd <= :d)
                        OR (v.daysUntilTrainingExpire IS NOT NULL AND v.daysUntilTrainingExpire <= :d)')
            ->setParameter('d', $maxDays)
            ->orderBy('v.daysUntilContractEnd', 'ASC')
            ->addOrderBy('v.daysUntilTrainingExpire', 'ASC')
            ->getQuery()
            ->getResult();
    }
}