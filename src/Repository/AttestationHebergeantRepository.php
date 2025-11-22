<?php

namespace App\Repository;

use App\Entity\AttestationHebergeant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AttestationHebergeant>
 *
 * @method AttestationHebergeant|null find($id, $lockMode = null, $lockVersion = null)
 * @method AttestationHebergeant|null findOneBy(array $criteria, array $orderBy = null)
 * @method AttestationHebergeant[]    findAll()
 * @method AttestationHebergeant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttestationHebergeantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttestationHebergeant::class);
    }

//    /**
//     * @return AttestationHebergeant[] Returns an array of AttestationHebergeant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AttestationHebergeant
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
