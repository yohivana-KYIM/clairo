<?php

namespace App\Service\EntityManagerServices;

use App\Entity\DemandeTitreVehicule;
use App\Repository\DemandeTitreVehiculeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class DemandeTitreVehiculeManagerService
{
    public function __construct(
        private readonly EntityManagerInterface            $entityManager,
        private readonly DemandeTitreVehiculeRepository $demandRepository,
        private readonly Security $security
    ) {}

    public function findOrCreateDemand(string $clientIp): DemandeTitreVehicule
    {
        $user = $this->security->getUser();
        $existingDemand = $this->demandRepository->findOneBy(['user' => $user], ['created_at' => 'DESC']);

        if ($existingDemand && ($existingDemand->getValidatedAt() !== null || !empty($existingDemand->getStatus()))) {
            return $existingDemand;
        }

        $newDemand = new DemandeTitreVehicule();
        $newDemand->setCreatedAt(new DateTime());
        $newDemand->setUser($user);
        $newDemand->setIp($clientIp);

        $this->entityManager->persist($newDemand);
        $this->entityManager->flush();

        return $newDemand;
    }

    public function validateDemand(DemandeTitreVehicule $demandeVehicule): void
    {
        $demandeVehicule->setValidatedAt(new DateTime());
        $demandeVehicule->setStatus(DemandeTitreVehicule::STATUS_EMPLOYER_REFERENCE);
        $this->entityManager->persist($demandeVehicule);
        $this->entityManager->flush();
    }

    public function getRedirectConditions(DemandeTitreVehicule $demandeTitreVehicule): array
    {
        return [
            [
                'condition' => ($demandeTitreVehicule->getEntreprise() === null && $demandeTitreVehicule->getIntervention() !== null),
                'route' => 'app_vehicule_intervention_edit',
                'params' => ['id' => $demandeTitreVehicule->getIntervention()?->getId()],
            ],

            [
                'condition' => ($demandeTitreVehicule->getEntreprise() === null && $demandeTitreVehicule->getIntervention() === null),
                'route' => 'app_vehicule_intervention_new',
                'params' => [],
            ],
            [
                'condition' => ($demandeTitreVehicule->getIntervention() !== null && $demandeTitreVehicule->getAdresse() === null),
                'route' => 'app_vehicule_adresse_new',
                'params' => [],
            ],
            [
                'condition' => ($demandeTitreVehicule->getIntervention() !== null && $demandeTitreVehicule->getAdresse() !== null && $demandeTitreVehicule->getInfocomplementaire() == null),
                'route' => 'app_vehicule_adresse_edit',
                'params' => ['id' => $demandeTitreVehicule->getAdresse()?->getId()],
            ],
            [
                'condition' => ($demandeTitreVehicule->getFiliation() === null),
                'route' => 'app_filiation_edit',
                'params' => ['id' => $demandeTitreVehicule->getFiliation()?->getId()],
            ],
            [
                'condition' => ($demandeTitreVehicule->getAdresse() === null),
                'route' => 'app_adresse_edit',
                'params' => ['id' => $demandeTitreVehicule->getAdresse()?->getId()],
            ],
            [
                'condition' => ($demandeTitreVehicule->getInfocomplementaire() === null),
                'route' => 'app_info_complementaire_edit',
                'params' => ['id' => $demandeTitreVehicule->getInfocomplementaire()?->getId()],
            ],
            [
                'condition' => ($demandeTitreVehicule->getDocpersonnel() === null),
                'route' => 'app_document_personnel_edit',
                'params' => ['id' => $demandeTitreVehicule->getDocpersonnel()?->getId()],
            ],
            [
                'condition' => ($demandeTitreVehicule->getDocumentprofessionnel() === null &&
                    $demandeTitreVehicule->getIntervention()?->getMotif() !== 'visite'),
                'route' => 'app_document_professionnel_edit',
                'params' => ['id' => $demandeTitreVehicule->getDocumentprofessionnel()?->getId()],
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
