<?php

namespace App\Repository;

use App\Entity\EntrepriseUnifiee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class EntrepriseUnifieeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntrepriseUnifiee::class);
    }

    /**
     * Recherche une entreprise par email du référent ou du suppléant
     * @throws NonUniqueResultException
     */
    public function findOneByEmailReferentOrSuppliant(string $email): ?EntrepriseUnifiee
    {
        // 1. On cherche côté référent
        $referent = $this->createQueryBuilder('e')
            ->where('e.emailReferent = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($referent) {
            return $referent;
        }

        // 2. Sinon on cherche côté suppléant
        return $this->createQueryBuilder('e')
            ->where('e.suppleant1 = :email')
            ->setParameter('email', $email)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
