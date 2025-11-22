<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\TodoUser;
use Doctrine\Persistence\ManagerRegistry;

class TodoUserRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TodoUser::class);
    }

    public function forUser(string $email): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.employeeEmail = :e')
            ->setParameter('e', $email)
            ->getQuery()->getResult();
    }
}