<?php

namespace App\Repository;

use App\Entity\ProblemeCarte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProblemeCarte>
 *
 * @method ProblemeCarte|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProblemeCarte|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProblemeCarte[]    findAll()
 * @method ProblemeCarte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProblemeCarteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProblemeCarte::class);
    }

//    /**
//     * @return ProblemeCarte[] Returns an array of ProblemeCarte objects
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

//    public function findOneBySomeField($value): ?ProblemeCarte
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
