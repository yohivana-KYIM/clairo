<?php

namespace App\Service\EntityManagerServices;

use App\Entity\DemandeTitreCirculation;
use App\Repository\DemandeTitreCirculationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class DemandeTitreCirculationManagerService
{
    public function __construct(
        private readonly EntityManagerInterface            $entityManager,
        private readonly DemandeTitreCirculationRepository $demandRepository,
        private readonly Security $security
    ) {}

    public function findOrCreateDemand(string $clientIp): DemandeTitreCirculation
    {
        $user = $this->security->getUser();
        $existingDemand = $this->demandRepository->findOneBy(['user' => $user], ['created_at' => 'DESC']);

        if ($existingDemand && ($existingDemand->getValidatedAt() !== null || !empty($existingDemand->getStatus()))) {
            return $existingDemand;
        }

        $newDemand = new DemandeTitreCirculation();
        $newDemand->setCreatedAt(new DateTime());
        $newDemand->setUser($user);
        $newDemand->setIp($clientIp);

        $this->entityManager->persist($newDemand);
        $this->entityManager->flush();

        return $newDemand;
    }

    public function validateDemand(DemandeTitreCirculation $demandeCirculation): void
    {
        $demandeCirculation->setValidatedAt(new DateTime());
        $demandeCirculation->setStatus(DemandeTitreCirculation::STATUS_EMPLOYER_REFERENCE);
        $this->entityManager->persist($demandeCirculation);
        $this->entityManager->flush();
    }

    public function getRedirectConditions(DemandeTitreCirculation $demandeTitreCirculation): array
    {
        return [
            [
                'condition' => ($demandeTitreCirculation->getEntreprise() === null),
                'route' => 'app_intervention_edit',
                'params' => ['id' => $demandeTitreCirculation->getIntervention()->getId()],
            ],
            [
                'condition' => ($demandeTitreCirculation->getEtatCivil() === null),
                'route' => 'app_etat_civil_edit',
                'params' => ['id' => $demandeTitreCirculation->getEtatCivil()?->getId()],
            ],
            [
                'condition' => ($demandeTitreCirculation->getFiliation() === null),
                'route' => 'app_filiation_edit',
                'params' => ['id' => $demandeTitreCirculation->getFiliation()?->getId()],
            ],
            [
                'condition' => ($demandeTitreCirculation->getAdresse() === null),
                'route' => 'app_adresse_edit',
                'params' => ['id' => $demandeTitreCirculation->getAdresse()?->getId()],
            ],
            [
                'condition' => ($demandeTitreCirculation->getInfocomplementaire() === null),
                'route' => 'app_info_complementaire_edit',
                'params' => ['id' => $demandeTitreCirculation->getInfocomplementaire()?->getId()],
            ],
            [
                'condition' => ($demandeTitreCirculation->getDocpersonnel() === null),
                'route' => 'app_document_personnel_edit',
                'params' => ['id' => $demandeTitreCirculation->getDocpersonnel()?->getId()],
            ],
            [
                'condition' => ($demandeTitreCirculation->getDocumentprofessionnel() === null &&
                    $demandeTitreCirculation->getIntervention()->getMotif() !== 'visite'),
                'route' => 'app_document_professionnel_edit',
                'params' => ['id' => $demandeTitreCirculation->getDocumentprofessionnel()?->getId()],
            ],
        ];
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}
