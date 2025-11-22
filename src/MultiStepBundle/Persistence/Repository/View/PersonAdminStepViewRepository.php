<?php

namespace App\MultiStepBundle\Persistence\Repository\View;

use App\MultiStepBundle\View\PersonAdminStepView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonAdminStepViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonAdminStepView::class);
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('v')
            ->getQuery()
            ->getResult();
    }

    public function findForAdminSorted(): array
    {
        $rankCase = "CASE v.status
        WHEN 'microcesame_ko' THEN 1
        WHEN 'investigation_ko' THEN 1
        WHEN 'payment_doc_ko' THEN 1
        WHEN 'bad_firm' THEN 2
        WHEN 'awaiting_reference' THEN 3
        WHEN 'pending' THEN 4
        WHEN 'approved' THEN 5
        WHEN 'awaiting_payment' THEN 6
        WHEN 'paid' THEN 7
        WHEN 'card_edited' THEN 8
        WHEN 'tc_temp_ok' THEN 9
        WHEN 'cerbere_ok' THEN 9
        WHEN 'draft' THEN 9
        WHEN 'deposit' THEN 9
        WHEN 'refused' THEN 10
        WHEN 'card_delivered' THEN 11
        ELSE 50 END";


        return $this->createQueryBuilder('v')
            ->addSelect("$rankCase AS HIDDEN status_rank")
            ->addOrderBy('status_rank', 'ASC')
            ->addOrderBy("SUBSTRING(v.requestDate, 1, 4)", 'DESC')  // année
            ->addOrderBy("SUBSTRING(v.requestDate, 6, 2)", 'DESC')  // mois
            ->addOrderBy("SUBSTRING(v.requestDate, 9, 2)", 'DESC')  // jour
            ->addOrderBy('v.stepId', 'DESC')      // filet si date non parseable
            ->addOrderBy('v.companyName', 'ASC')
            ->addOrderBy('v.employeeLastName', 'ASC')
            ->addOrderBy('v.employeeFirstName', 'ASC')
            ->getQuery()->getResult();
    }
}
