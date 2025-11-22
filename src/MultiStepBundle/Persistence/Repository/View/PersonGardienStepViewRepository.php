<?php

namespace App\MultiStepBundle\Persistence\Repository\View;

use App\MultiStepBundle\View\PersonGardienStepView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonGardienStepViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonGardienStepView::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('v')
            ->getQuery()
            ->getResult();
    }

    public function findForGardienSorted(): array
    {
        $qb = $this->createQueryBuilder('v');

        $rankCase = "
        CASE
            WHEN v.status = 'card_edited' THEN 1
            WHEN v.status = 'card_delivered' THEN 10
            ELSE 50
        END
    ";

        // ✅ utiliser CURRENT_DATE() au lieu de NULL (Doctrine ne comprend pas NULL dans un COALESCE)
        $dateExpr = "
        COALESCE(
            STR_TO_DATE(v.requestDate, '%Y-%m-%d'),
            STR_TO_DATE(v.requestDate, '%d %M %Y'),
            STR_TO_DATE(v.requestDate, '%d %m %Y'),
            CURRENT_DATE()
        )
    ";

        $qb->addSelect("$rankCase AS HIDDEN status_rank")
            ->addSelect("$dateExpr AS HIDDEN req_dt")
            ->addOrderBy('status_rank', 'ASC')
            ->addOrderBy('req_dt', 'ASC')
            ->addOrderBy('v.stepId', 'ASC')
            ->addOrderBy('v.employeeLastName', 'ASC')
            ->addOrderBy('v.employeeFirstName', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
