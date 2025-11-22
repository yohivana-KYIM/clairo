<?php

namespace App\Repository\Dashboard;

use App\Entity\Dashboard\ChartDecisionEvolution;
use Doctrine\Persistence\ManagerRegistry;

class ChartDecisionEvolutionRepository extends AbstractViewRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartDecisionEvolution::class);
    }

    public function forDecision(string $decision): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.accessDecision = :d')
            ->setParameter('d', $decision)
            ->orderBy('v.month', 'ASC')
            ->getQuery()->getResult();
    }
}