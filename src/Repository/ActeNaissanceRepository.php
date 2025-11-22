<?php

namespace App\Repository;

use App\Entity\ActeNaissance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ActeNaissance>
 *
 * @method ActeNaissance|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActeNaissance|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActeNaissance[]    findAll()
 * @method ActeNaissance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActeNaissanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActeNaissance::class);
    }

//    /**
//     * @return ActeNaissance[] Returns an array of ActeNaissance objects
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

//    public function findOneBySomeField($value): ?ActeNaissance
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
