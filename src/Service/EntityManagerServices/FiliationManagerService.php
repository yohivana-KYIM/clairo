<?php

namespace App\Service\EntityManagerServices;

use App\Entity\Filiation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class FiliationManagerService
{
    private readonly EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Filiation::class)->findAll();
    }

    public function getUserLatestRequest(User $user)
    {
        return $user->getDemandes()->last();
    }

    public function handleFiliationForm(Filiation $filiation, $demandeTitreCirculation): void
    {
        $demandeTitreCirculation->setFiliation($filiation);
        $filiation->setSubmited(true);

        $this->entityManager->persist($filiation);
        $this->entityManager->flush();
    }

    public function determineNextRoute($demandeTitreCirculation): array
    {
        $adresse = $demandeTitreCirculation->getAdresse();

        if ($adresse) {
            return ['name' => 'app_adresse_edit', 'params' => ['id' => $adresse->getId()]];
        }

        return ['name' => 'app_adresse_new', 'params' => []];
    }

    public function prepareDemandeDetails($demandeTitreCirculation): array
    {
        return [
            'demande' => $demandeTitreCirculation,
            'intervention' => $demandeTitreCirculation->getIntervention(),
            'etatCivil' => $demandeTitreCirculation->getEtatCivil(),
            'adresse' => $demandeTitreCirculation->getAdresse(),
            'infoComplementaire' => $demandeTitreCirculation->getInfocomplementaire(),
            'documentPersonnel' => $demandeTitreCirculation->getDocpersonnel(),
            'documentProfessionnel' => $demandeTitreCirculation->getDocumentprofessionnel(),
        ];
    }

    public function delete(Filiation $filiation): void
    {
        $this->entityManager->remove($filiation);
        $this->entityManager->flush();
    }
}
