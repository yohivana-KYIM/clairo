<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Repository;

use App\MultiStepBundle\Entity\PersonFlattenedStepData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonFlattenedStepDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonFlattenedStepData::class);
    }

    /**
     * Exemple : trouver les enregistrements par numéro CNI
     */
    public function findByNumeroCni(string $numeroCni): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.numero_cni = :numeroCni')
            ->setParameter('numeroCni', $numeroCni)
            ->getQuery()
            ->getResult();
    }

    /**
     * Exemple : filtrer les entrées par nom ou prénom (like %term%)
     */
    public function searchByName(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->where('LOWER(p.employee_first_name) LIKE :term OR LOWER(p.employee_last_name) LIKE :term')
            ->setParameter('term', '%' . strtolower($term) . '%')
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();
    }
}