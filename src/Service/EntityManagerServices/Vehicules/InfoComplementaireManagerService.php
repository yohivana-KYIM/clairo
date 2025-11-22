<?php

namespace App\Service\EntityManagerServices\Vehicules;

use App\Entity\DemandeTitreVehicule;
use App\Entity\InfoComplementaireVehicule;
use App\Entity\User;
use App\Repository\InfoComplementaireRepository;
use Doctrine\ORM\EntityManagerInterface;

class InfoComplementaireManagerService
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly InfoComplementaireRepository $infoComplementaireRepository)
    {
    }

    public function getAll(): array
    {
        return $this->infoComplementaireRepository->findAll();
    }

    public function getUserLatestRequest(User $user)
    {
        return $user->getDemandeVehicules()->last();
    }

    public function saveInfoComplementaire(InfoComplementaireVehicule $infoComplementaire, DemandeTitreVehicule $demandeTitreCirculation): void
    {
        $user = $infoComplementaire->getEmail() ?? $demandeTitreCirculation->getUser()->getEmail();
        $infoComplementaire->setEmail($user);

        $demandeTitreCirculation->setInfocomplementaire($infoComplementaire);
        $infoComplementaire->setSubmited(true);

        $this->entityManager->persist($infoComplementaire);
        $this->entityManager->flush();
    }

    public function delete(InfoComplementaireVehicule $infoComplementaire): void
    {
        $this->entityManager->remove($infoComplementaire);
        $this->entityManager->flush();
    }

    public function prepareDemandeDetails(DemandeTitreVehicule $demandeTitreCirculation): array
    {
        return [
            'demande' => $demandeTitreCirculation,
            'intervention' => $demandeTitreCirculation->getIntervention(),
            'etatCivil' => $demandeTitreCirculation->getEtatCivil(),
            'filiation' => $demandeTitreCirculation->getFiliation(),
            'adresse' => $demandeTitreCirculation->getAdresse(),
            'documentPersonnel' => $demandeTitreCirculation->getDocpersonnel(),
            'documentProfessionnel' => $demandeTitreCirculation->getDocumentprofessionnel(),
        ];
    }
}
