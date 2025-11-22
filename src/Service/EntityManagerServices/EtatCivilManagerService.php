<?php

namespace App\Service\EntityManagerServices;

use App\Entity\EtatCivil;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class EtatCivilManagerService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array<string, mixed>
     */
    public function initializeEtatCivilForUser(User $user): array
    {
        $demandeTitreCirculation = $user->getDemandes()->last();

        return [
            'demande' => $demandeTitreCirculation,
            'intervention' => $demandeTitreCirculation->getIntervention(),
            'filiation' => $demandeTitreCirculation->getFiliation(),
            'adresse' => $demandeTitreCirculation->getAdresse(),
            'infoComplementaire' => $demandeTitreCirculation->getInfocomplementaire(),
            'documentPersonnel' => $demandeTitreCirculation->getDocpersonnel(),
            'documentProfessionnel' => $demandeTitreCirculation->getDocumentprofessionnel(),
        ];
    }

    public function saveEtatCivil(EtatCivil $etatCivil, $demandeTitreCirculation): void
    {
        $demandeTitreCirculation->setEtatCivil($etatCivil);
        $etatCivil->setSubmited(true);

        $this->entityManager->persist($etatCivil);
        $this->entityManager->flush();
    }

    public function deleteEtatCivil(EtatCivil $etatCivil): void
    {
        $this->entityManager->remove($etatCivil);
        $this->entityManager->flush();
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}
