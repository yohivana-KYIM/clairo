<?php

namespace App\MultiStepBundle\Persistence\Repository\View;

use App\MultiStepBundle\View\PersonSdriStepView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonSdriStepViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonSdriStepView::class);
    }

    /**
     * Pour le SDRI → tout est visible, donc simple :
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('v')
            ->getQuery()
            ->getResult();
    }

    public function findForSdriSorted(): array
    {
        $rankCase = "CASE v.status
        WHEN 'awaiting_reference' THEN 1
        WHEN 'pending' THEN 2
        WHEN 'awaiting_info' THEN 3
        WHEN 'approved' THEN 4
        WHEN 'provisioned' THEN 5
        WHEN 'microcesame' THEN 6
        WHEN 'enquete_prealable' THEN 7
        WHEN 'tc_temp_ok' THEN 8
        WHEN 'cerbere_sent' THEN 9
        WHEN 'cerbere_ko' THEN 10
        WHEN 'microcesame_ko' THEN 10
        WHEN 'investigation_ko' THEN 10
        WHEN 'payment_doc_ko' THEN 10
        WHEN 'cerbere_ok' THEN 11
        WHEN 'awaiting_payment' THEN 12
        WHEN 'refused' THEN 19
        ELSE 50 END";

        $dateExpr = "COALESCE(
        STR_TO_DATE(v.requestDate, '%Y-%m-%d'),
        STR_TO_DATE(v.requestDate, '%d %M %Y'),
        STR_TO_DATE(v.requestDate, '%d %m %Y'),
        CURRENT_DATE()
    )";

        return $this->createQueryBuilder('v')
            ->addSelect("$rankCase AS HIDDEN status_rank")
            ->addOrderBy('status_rank', 'ASC')
            ->addOrderBy("SUBSTRING(v.requestDate, 1, 4)", 'DESC')  // année
            ->addOrderBy("SUBSTRING(v.requestDate, 6, 2)", 'DESC')  // mois
            ->addOrderBy("SUBSTRING(v.requestDate, 9, 2)", 'DESC')  // jour
            ->addOrderBy('v.stepId', 'ASC')
            ->addOrderBy('v.companyName', 'ASC')
            ->addOrderBy('v.employeeLastName', 'ASC')
            ->addOrderBy('v.employeeFirstName', 'ASC')
            ->getQuery()->getResult();
    }
}
