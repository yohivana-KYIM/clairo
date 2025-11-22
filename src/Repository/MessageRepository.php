<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findUserMessages(User $user)
    {
        return $this->createQueryBuilder('m')
            ->where('m.receiver = :user')
            ->andWhere('m.sender IS NOT NULL')
            ->setParameter('user', $user)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAppNotifications($user)
    {
        return $this->createQueryBuilder('m')
            ->where('m.receiver = :user')
            ->andWhere('m.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', 'notification')
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
