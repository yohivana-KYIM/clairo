<?php

namespace App\Repository;

use App\Entity\Atex0;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Atex0>
 *
 * @method Atex0|null find($id, $lockMode = null, $lockVersion = null)
 * @method Atex0|null findOneBy(array $criteria, array $orderBy = null)
 * @method Atex0[]    findAll()
 * @method Atex0[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Atex0Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Atex0::class);
    }

//    /**
//     * @return Atex0[] Returns an array of Atex0 objects
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

//    public function findOneBySomeField($value): ?Atex0
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
