<?php

namespace App\Repository;

use App\Entity\Gies2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gies2>
 *
 * @method Gies2|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gies2|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gies2[]    findAll()
 * @method Gies2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Gies2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gies2::class);
    }

//    /**
//     * @return Gies2[] Returns an array of Gies2 objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Gies2
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
