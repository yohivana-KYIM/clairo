<?php

namespace App\Repository;

use App\Entity\AutreDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AutreDocument>
 *
 * @method AutreDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method AutreDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method AutreDocument[]    findAll()
 * @method AutreDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutreDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AutreDocument::class);
    }

//    /**
//     * @return AutreDocument[] Returns an array of AutreDocument objects
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

//    public function findOneBySomeField($value): ?AutreDocument
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
