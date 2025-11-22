<?php

namespace App\Repository;

use App\Entity\DocumentPersonnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DocumentPersonnel>
 *
 * @method DocumentPersonnel|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentPersonnel|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentPersonnel[]    findAll()
 * @method DocumentPersonnel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentPersonnelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentPersonnel::class);
    }
}
