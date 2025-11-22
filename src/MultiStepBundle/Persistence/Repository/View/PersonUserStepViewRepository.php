<?php

namespace App\MultiStepBundle\Persistence\Repository\View;

use App\MultiStepBundle\View\PersonUserStepView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonUserStepViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonUserStepView::class);
    }

    public function findStepsForUser(int $userId): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.userId = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findStepsForUserSorted(int $userId): array
    {
        // Pondération des statuts (uniquement ceux exposés par v_person_user_steps)
        $rankCase = "CASE v.status
            WHEN 'awaiting_payment' THEN 1
            WHEN 'draft' THEN 2
            WHEN 'deposit' THEN 3
            WHEN 'awaiting_reference' THEN 4
            WHEN 'pending' THEN 5
            WHEN 'approved' THEN 6
            WHEN 'bad_firm' THEN 7
            WHEN 'payment_doc_ko' THEN 7
            WHEN 'microcesame_ko' THEN 7
            WHEN 'investigation_ko' THEN 7
            WHEN 'tc_temp_ok' THEN 8
            WHEN 'cerbere_ok' THEN 8
            WHEN 'paid' THEN 9
            WHEN 'card_edited' THEN 10
            WHEN 'card_delivered' THEN 11
            WHEN 'refused' THEN 12
            ELSE 50 END";

        // request_date est une chaîne issue du JSON -> on essaye de la parser
        $dateExpr = "COALESCE(
            STR_TO_DATE(v.requestDate, '%Y-%m-%d'),
            STR_TO_DATE(v.requestDate, '%d %M %Y'),
            STR_TO_DATE(v.requestDate, '%d %m %Y'),
            CURRENT_DATE()
        )";

        return $this->createQueryBuilder('v')
            ->andWhere('v.userId = :id')->setParameter('id', $userId)
            ->addSelect("$rankCase AS HIDDEN status_rank")
            ->addSelect("$dateExpr AS HIDDEN req_dt")
            ->addOrderBy('status_rank', 'ASC')          // ordre métier
            ->addOrderBy('req_dt', 'ASC')               // plus anciennes d'abord
            ->addOrderBy('v.stepId', 'ASC')             // filet si date non parseable
            ->addOrderBy('v.companyName', 'ASC')
            ->addOrderBy('v.employeeLastName', 'ASC')
            ->addOrderBy('v.employeeFirstName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
