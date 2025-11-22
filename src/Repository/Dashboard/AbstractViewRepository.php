<?php

namespace App\Repository\Dashboard;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Common helpers for read-only view repositories.
 */
abstract class AbstractViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    /**
     * Returns all rows (small views are fine; for large ones, add filters).
     */
    public function all(): array
    {
        return $this->createQueryBuilder('v')->getQuery()->getResult();
    }
}