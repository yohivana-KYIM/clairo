<?php

namespace App\Repository;

use App\Entity\DocumentIdentite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentIdentite>
 *
 * @method DocumentIdentite|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentIdentite|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentIdentite[]    findAll()
 * @method DocumentIdentite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentIdentiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentIdentite::class);
    }

//    /**
//     * @return DocumentIdentite[] Returns an array of DocumentIdentite objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DocumentIdentite
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
