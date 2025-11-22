<?php

namespace App\Repository;

use App\Entity\UserValidationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserValidationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserValidationToken::class);
    }

    public function findExpiredTokens(): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.expiresAt < :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }
}
