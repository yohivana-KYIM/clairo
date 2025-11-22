<?php

namespace App\MultiStepBundle\Persistence;

use App\MultiStepBundle\Application\Enum\StepDataStatus;
use App\MultiStepBundle\Application\PersonAccessWorkflowService;
use App\MultiStepBundle\Application\VehicleAccessWorkflowService;
use App\MultiStepBundle\Entity\StepData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class SingleTablePersistenceStrategy extends PersistanceStrategy
{

    public function __construct(private readonly Security $security, RequestStack $requestStack, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($requestStack);
    }

    public function saveData(string $stepId, array $data): array
    {
        $stepId = $this->generateRequestIdentifier($data);
        $class = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'];
        $stepType = match ($class) {
            VehicleAccessWorkflowService::class => 'vehicle',
            PersonAccessWorkflowService::class => 'person',
        };
        $entity = $this->entityManager->getRepository(StepData::class)->findOneBy(['stepNumber' => $stepId]);
        if (!$entity) {
            $entity = new StepData();
            $entity->setPersistanceType('single_table');
            $entity->setStepNumber($stepId);
            $entity->setStepType($stepType);
            if (array_key_exists('step_id', $data)) {
                $entity->setStepId($data['step_id']);
            }
        }
        if (empty($entity->getStatus())) {
            $entity->setStatus(StepDataStatus::DRAFT);
        }
        $entity->setData($data);
        $entity->setUser($this->security->getUser());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        $data['step_id'] = $entity->getStepId();
        $data['entity'] = $entity;

        return $data;
    }

    public function loadData(string $stepId): array
    {
        $entity = $this->entityManager->getRepository(StepData::class)->find(['step_number' => $stepId]);
        return $entity ? $entity->getData() : [];
    }

    public function stepNameExistsForUser(string $stepId): bool
    {
        return $this->entityManager->getRepository(StepData::class)->findOneBy([
                'stepNumber' => $stepId,
                'user' => $this->security->getUser()
            ]) !== null;
    }

    /**
     * Génère une clé unique pour la demande,
     * que ce soit pour une personne ou un véhicule.
     *
     * Personne : NOM-PRENOM-SIRET-MATRICULE-YYYYMMDD
     * Véhicule : NOM-PRENOM-SIRET-IMMATRICULATION-YYYYMMDD
     *
     * @param array $data Données multi-step du formulaire.
     * @return string Identifiant unique.
     */
    public function generateRequestIdentifier(array $data): string
    {
        // Détection du type de formulaire (person ou vehicle)
        $isVehicle = isset($data['vehicle_step_one']);

        if ($isVehicle) {
            // Données véhicule
            $responsibleName = $data['vehicle_step_one']['responsible_name'] ?? '';
            [$lastName, $firstName] = array_pad(explode(' ', $responsibleName, 2), 2, '');

            $siret = $data['vehicle_step_one']['siret_number'] ?? '';
            $registration = $data['vehicle_step_two']['registration_number'] ?? '';
            $reference = strtoupper(str_replace(' ', '', trim($registration)));

            $requestDate = $data['vehicle_step_one']['request_date'] ?? '';
        } else {
            // Données personne
            $lastName = $data['person_step_two']['employee_last_name'] ?? '';
            $firstName = $data['person_step_two']['employee_first_name'] ?? '';
            $siret = $data['person_step_one']['siret'] ?? '';
            $matricule = $data['person_step_two']['matricule'] ?? '';
            $reference = strtoupper(trim($matricule));

            $requestDate = $data['person_step_one']['request_date'] ?? '';
        }

        // Formatage de la date
        $datePart = '';
        if (!empty($requestDate)) {
            $datePart = date('Ymd', strtotime($requestDate));
        }

        // Construction finale de l'identifiant
        return sprintf(
            '%s-%s-%s-%s-%s',
            strtoupper(trim($lastName)),
            ucfirst(strtolower(trim($firstName))),
            trim($siret),
            $reference,
            $datePart
        );
    }
}
