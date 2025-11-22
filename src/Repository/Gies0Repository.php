<?php

namespace App\Repository;

use App\Entity\Gies0;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gies0>
 *
 * @method Gies0|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gies0|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gies0[]    findAll()
 * @method Gies0[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Gies0Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gies0::class);
    }

//    /**
//     * @return Gies0[] Returns an array of Gies0 objects
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

//    public function findOneBySomeField($value): ?Gies0
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
