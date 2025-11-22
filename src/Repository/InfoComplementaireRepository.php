<?php

namespace App\Repository;

use App\Entity\InfoComplementaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InfoComplementaire>
 *
 * @method InfoComplementaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method InfoComplementaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method InfoComplementaire[]    findAll()
 * @method InfoComplementaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InfoComplementaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InfoComplementaire::class);
    }
}
