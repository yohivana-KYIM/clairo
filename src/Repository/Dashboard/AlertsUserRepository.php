<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\AlertsUser;
use Doctrine\Persistence\ManagerRegistry;

class AlertsUserRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlertsUser::class);
    }

    public function forUser(string $email): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.employeeEmail = :e')
            ->setParameter('e', $email)
            ->getQuery()->getResult();
    }
}
