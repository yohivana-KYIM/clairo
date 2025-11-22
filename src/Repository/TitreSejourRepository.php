<?php

namespace App\Repository;

use App\Entity\TitreSejour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TitreSejour>
 *
 * @method TitreSejour|null find($id, $lockMode = null, $lockVersion = null)
 * @method TitreSejour|null findOneBy(array $criteria, array $orderBy = null)
 * @method TitreSejour[]    findAll()
 * @method TitreSejour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TitreSejourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TitreSejour::class);
    }

//    /**
//     * @return TitreSejour[] Returns an array of TitreSejour objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TitreSejour
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
