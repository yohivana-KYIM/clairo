<?php

namespace App\MultiStepBundle\Persistence\Repository\View;

use App\MultiStepBundle\View\PersonRefsecuStepView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonRefsecuStepViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonRefsecuStepView::class);
    }

    /**
     * Filtrage logique côté SELECT :
     * - Vérifie que refsecu_id match l'utilisateur connecté
     * - Vérifie les conditions métier (fait côté WHERE)
     */
    public function findStepsForRefsecu(int $refsecuId, string $refsecuSiret, string $refsecuEmail): array
    {
        return $this->createQueryBuilder('v')
            ->where(
                'v.refsecuId = :refsecuId
                OR v.securityOfficerEmail = :refsecuEmail
                 OR v.siretData = :refsecuSiret
                 OR v.emailReferentEntreprise = :refsecuEmail'
            )
            ->setParameter('refsecuId', $refsecuId)
            ->setParameter('refsecuEmail', $refsecuEmail)
            ->setParameter('refsecuSiret', $refsecuSiret)
            ->getQuery()
            ->getResult();
    }

    public function findForRefsecuSorted(int $refsecuId, string $refsecuSiret, string $refsecuEmail): array
    {
        $rankCase = "CASE v.status
        WHEN 'awaiting_reference' THEN 1
        WHEN 'awaiting_payment' THEN 2
        WHEN 'paid' THEN 3
        WHEN 'pending' THEN 4
        WHEN 'approved' THEN 5
        WHEN 'payment_doc_ko' THEN 7
        WHEN 'bad_firm' THEN 7
        WHEN 'refused' THEN 9
        WHEN 'cerbere_ok' THEN 10
        WHEN 'card_edited' THEN 11
        WHEN 'card_delivered' THEN 12
        ELSE 50 END";

        $dateExpr = "COALESCE(
        STR_TO_DATE(v.requestDate, '%Y-%m-%d'),
        STR_TO_DATE(v.requestDate, '%d %M %Y'),
        STR_TO_DATE(v.requestDate, '%d %m %Y'),
        CURRENT_DATE()
    )";

        return $this->createQueryBuilder('v')
            ->where(
                'v.refsecuId = :refsecuId
                OR v.securityOfficerEmail = :refsecuEmail
                 OR v.siretData = :refsecuSiret
                 OR v.emailReferentEntreprise = :refsecuEmail'
            )
            ->setParameter('refsecuId', $refsecuId)
            ->setParameter('refsecuEmail', $refsecuEmail)
            ->setParameter('refsecuSiret', $refsecuSiret)
            ->addSelect("$rankCase AS HIDDEN status_rank")
            ->addSelect("$dateExpr AS HIDDEN req_dt")
            ->addOrderBy('status_rank', 'ASC')
            ->addOrderBy('v.companyName', 'ASC')
            ->addOrderBy('req_dt', 'ASC')
            ->addOrderBy('v.stepId', 'ASC')
            ->addOrderBy('v.employeeLastName', 'ASC')
            ->addOrderBy('v.employeeFirstName', 'ASC')
            ->getQuery()->getResult();
    }
}
