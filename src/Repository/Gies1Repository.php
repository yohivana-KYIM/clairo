<?php

namespace App\Repository;

use App\Entity\Gies1;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gies1>
 *
 * @method Gies1|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gies1|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gies1[]    findAll()
 * @method Gies1[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Gies1Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gies1::class);
    }

//    /**
//     * @return Gies1[] Returns an array of Gies1 objects
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

//    public function findOneBySomeField($value): ?Gies1
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
