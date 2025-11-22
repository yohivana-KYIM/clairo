<?php

namespace App\Repository;

use App\Entity\DemandeTitreCirculation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class DemandeTitreCirculationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeTitreCirculation::class);
    }

    public function findOneByUser($user): ?DemandeTitreCirculation
    {
        return $this->findOneBy(['user' => $user]);
    }

    /**
     * Creates the base query for filtering by statuses.
     */
    private function createBaseQuery(array $statuses): QueryBuilder
    {
        return $this->createQueryBuilder('d')
            ->where('d.status IN (:statuses)')
            ->setParameter('statuses', $statuses);
    }

    /**
     * Get all demandes filtered by statuses.
     */
    public function findByStatuses(array $statuses): array
    {
        return $this->createBaseQuery($statuses)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the count of demandes filtered by statuses.
     */
    public function countByStatuses(array $statuses): int
    {
        return $this->createBaseQuery($statuses)
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get all validated demandes filtered by statuses.
     */
    public function findValidatedByStatuses(array $statuses): array
    {
        return $this->createBaseQuery($statuses)
            ->andWhere('d.validated_at IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the count of validated demandes filtered by statuses.
     */
    public function countValidatedByStatuses(array $statuses): int
    {
        return $this->createBaseQuery($statuses)
            ->select('COUNT(d.id)')
            ->andWhere('d.validated_at IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get all invalid demandes filtered by statuses.
     */
    public function findInvalidByStatuses(array $statuses): array
    {
        return $this->createBaseQuery($statuses)
            ->andWhere('d.validated_at IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get the count of invalid demandes filtered by statuses.
     */
    public function countInvalidByStatuses(array $statuses): int
    {
        return $this->createBaseQuery($statuses)
            ->select('COUNT(d.id)')
            ->andWhere('d.validated_at IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
