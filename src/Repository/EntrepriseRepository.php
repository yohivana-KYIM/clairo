<?php

namespace App\Repository;

use App\Entity\Entreprise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Entreprise>
 *
 * @method Entreprise|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entreprise|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entreprise[]    findAll()
 * @method Entreprise[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntrepriseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entreprise::class);
    }


    public function findByRef($user): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.emailReferent = :email')
            ->orWhere('e.suppleant1 = :email')
            ->orWhere('e.suppleant2 = :email')
            ->setParameter('email', $user)
            ->getQuery()
            ->getResult();
    }
    public function findByName(?string $nom = ''): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.nom like :nom')
            ->setParameter('nom', '%'.$nom.'%')
            ->getQuery()
            ->getResult();
    }
    public function findBySiret(?string $siret = ''): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.siret like  :siret')
            ->setParameter('siret', '%'.$siret.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneBySiret(?string $siret = ''): ?Entreprise
    {
        return $this->createQueryBuilder('e')
            ->where('e.siret like  :siret')
            ->setParameter('siret', '%'.$siret.'%')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
