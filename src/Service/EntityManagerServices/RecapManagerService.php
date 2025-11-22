<?php

namespace App\Service\EntityManagerServices;

use App\Entity\DemandeTitreCirculation;
use App\Entity\User;
use App\Form\AdresseType;
use App\Form\DocumentPersonnelType;
use App\Form\DocumentProfessionnelType;
use App\Form\EtatCivilType;
use App\Form\FiliationType;
use App\Form\InfoComplementaireType;
use App\Form\InterventionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class RecapManagerService
{

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly FormFactoryInterface $formFactory)
    {
    }

    public function prepareRecapData(?UserInterface $user): array
    {
        assert($user instanceof User);
        $demandeTitreCirculation = $user->getDemandes()->last();

        return $this->prepareRecapForms($demandeTitreCirculation);
    }

    public function prepareRecapDataById(int $id): array
    {
        $demandeTitreCirculation = $this->entityManager->getRepository(DemandeTitreCirculation::class)->find($id);

        return $this->prepareRecapForms($demandeTitreCirculation);
    }

    public function prepareRecapForms(DemandeTitreCirculation $demandeTitreCirculation): array
    {
        $forms = [
            'formIntervention' => $this->createFormData($demandeTitreCirculation->getIntervention(), InterventionType::class),
            'formEtatCivil' => $this->createFormData($demandeTitreCirculation->getEtatCivil(), EtatCivilType::class),
            'formFiliation' => $this->createFormData($demandeTitreCirculation->getFiliation(), FiliationType::class),
            'formAdresse' => $this->createFormData($demandeTitreCirculation->getAdresse(), AdresseType::class),
            'formInfoComplementaire' => $this->createFormData($demandeTitreCirculation->getInfoComplementaire(), InfoComplementaireType::class),
            'formDocumentPerso' => $this->createFormData($demandeTitreCirculation->getDocpersonnel(), DocumentPersonnelType::class),
            'formDocumentPro' => $this->createFormData($demandeTitreCirculation->getDocumentprofessionnel(), DocumentProfessionnelType::class),
        ];

        $entreprise = $demandeTitreCirculation->getEntreprise();

        return array_merge($forms, [
            'derniereIntervention' => $demandeTitreCirculation->getIntervention(),
            'dernierEtatCivil' => $demandeTitreCirculation->getEtatCivil(),
            'derniereFiliation' => $demandeTitreCirculation->getFiliation(),
            'derniereAdresse' => $demandeTitreCirculation->getAdresse(),
            'derniereInfoComplementaire' => $demandeTitreCirculation->getInfoComplementaire(),
            'dernierDocPersonnel' => $demandeTitreCirculation->getDocpersonnel(),
            'dernierDocProfessionnel' => $demandeTitreCirculation->getDocumentprofessionnel(),
            'demandeTitreCirculation' => $demandeTitreCirculation,
            'entreprise' => $entreprise?->getNom(),
        ]);
    }

    public function updateRecapStatus(?UserInterface $user): void
    {
        assert($user instanceof User);
        $demandeTitreCirculation = $user->getDemandes()->last();
        $currentStatus = $demandeTitreCirculation->getStatus();

        switch ($currentStatus) {
            case DemandeTitreCirculation::STATUS_AWAITING:
                $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_DEPOSIT);
                break;
            case DemandeTitreCirculation::STATUS_BAD_FIRM:
                $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_BAD_FIRM);
                break;
        }

        $this->entityManager->persist($demandeTitreCirculation);
        $this->entityManager->flush();
    }

    public function getLastRoute(Request $request): string
    {
        $referer = $request->headers->get('referer');
        return $referer ? basename($referer) : '';
    }

    private function createFormData($entity, string $formType): ?FormView
    {
        if ($entity === null) {
            return null;
        }

        return $this->formFactory->create($formType, $entity)->createView();
    }
}
