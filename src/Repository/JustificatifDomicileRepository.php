<?php

namespace App\Repository;

use App\Entity\JustificatifDomicile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JustificatifDomicile>
 *
 * @method JustificatifDomicile|null find($id, $lockMode = null, $lockVersion = null)
 * @method JustificatifDomicile|null findOneBy(array $criteria, array $orderBy = null)
 * @method JustificatifDomicile[]    findAll()
 * @method JustificatifDomicile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JustificatifDomicileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JustificatifDomicile::class);
    }

//    /**
//     * @return JustificatifDomicile[] Returns an array of JustificatifDomicile objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?JustificatifDomicile
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
