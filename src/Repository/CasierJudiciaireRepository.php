<?php

namespace App\Repository;

use App\Entity\CasierJudiciaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CasierJudiciaire>
 *
 * @method CasierJudiciaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method CasierJudiciaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method CasierJudiciaire[]    findAll()
 * @method CasierJudiciaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CasierJudiciaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasierJudiciaire::class);
    }

//    /**
//     * @return CasierJudiciaire[] Returns an array of CasierJudiciaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CasierJudiciaire
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
