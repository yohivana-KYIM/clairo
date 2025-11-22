<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\RecentUserRequests;
use Doctrine\Persistence\ManagerRegistry;

class RecentUserRequestsRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecentUserRequests::class);
    }

    public function recentForUser(string $email, ?int $maxDays = null): array
    {
        $qb = $this->createQueryBuilder('v')
            ->andWhere('v.employeeEmail = :e')
            ->setParameter('e', $email)
            ->orderBy('v.requestDate', 'DESC');

        if ($maxDays !== null) {
            $qb->andWhere('v.daysSince <= :d')->setParameter('d', $maxDays);
        }

        return $qb->getQuery()->getResult();
    }
}