<?php

namespace App\Repository;

use App\Entity\DocumentProfessionnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentProfessionnel>
 *
 * @method DocumentProfessionnel|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentProfessionnel|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentProfessionnel[]    findAll()
 * @method DocumentProfessionnel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentProfessionnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentProfessionnel::class);
    }

//    /**
//     * @return DocumentProfessionnel[] Returns an array of DocumentProfessionnel objects
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

//    public function findOneBySomeField($value): ?DocumentProfessionnel
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
