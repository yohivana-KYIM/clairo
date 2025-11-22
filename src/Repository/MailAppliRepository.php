<?php

namespace App\Repository;

use App\Entity\MailAppli;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MailAppli>
 *
 * @method MailAppli|null find($id, $lockMode = null, $lockVersion = null)
 * @method MailAppli|null findOneBy(array $criteria, array $orderBy = null)
 * @method MailAppli[]    findAll()
 * @method MailAppli[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MailAppliRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MailAppli::class);
    }

//    /**
//     * @return MailAppli[] Returns an array of MailAppli objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MailAppli
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
