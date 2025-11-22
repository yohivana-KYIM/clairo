<?php

namespace App\Repository;

use App\Entity\IdentiteHebergeant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IdentiteHebergeant>
 *
 * @method IdentiteHebergeant|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdentiteHebergeant|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdentiteHebergeant[]    findAll()
 * @method IdentiteHebergeant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdentiteHebergeantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdentiteHebergeant::class);
    }

//    /**
//     * @return IdentiteHebergeant[] Returns an array of IdentiteHebergeant objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IdentiteHebergeant
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
