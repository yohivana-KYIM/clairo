<?php

namespace App\Repository;

use App\Entity\PhotoIdentite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PhotoIdentite>
 *
 * @method PhotoIdentite|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhotoIdentite|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhotoIdentite[]    findAll()
 * @method PhotoIdentite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoIdentiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PhotoIdentite::class);
    }

//    /**
//     * @return PhotoIdentite[] Returns an array of PhotoIdentite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PhotoIdentite
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
