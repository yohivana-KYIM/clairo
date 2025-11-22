<?php

namespace App\Service\EntityManagerServices;

use App\MultiStepBundle\Entity\PersonFlattenedStepData;
use App\MultiStepBundle\Infrastructure\Symfony\Workflow\StepDataWorkflowService;
use DateTime;
use App\Entity\DemandeTitreCirculation;
use App\Entity\ProblemeCarte;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class ProblemeCarteManagerService
{

    public function __construct(
        private readonly  EntityManagerInterface $entityManager,
        private readonly  StepDataWorkflowService $workflow,
    )
    {
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(ProblemeCarte::class)->findAll();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws TransportExceptionInterface
     */
    public function saveNewProblemeCarte(ProblemeCarte $problemeCarte, User $user): void
    {
        $problemeCarte->setUser($user);
        $problemeCarte->setCreatedAt(new DateTime());

        // 1️⃣ chercher une carte livrée avec le même email
        $existingCard = $this->entityManager
            ->getRepository(PersonFlattenedStepData::class)
            ->findOneBy([
                'employeeEmail' => $problemeCarte->getEmail(),
                'status' => 'card_delivered'
            ]);

        if ($existingCard) {
            // 2️⃣ appliquer la transition vers awaiting_payment
            $stepData = $existingCard->getStepData();
            $this->workflow->applyTransition($stepData, StepDataWorkflowService::TRANSITION_REEDIT_CARD);

            // marquer le problème carte comme lié à une demande en attente de paiement
            $problemeCarte->setStatus($stepData->getStatus());
        } else {
            // sinon → status par défaut
            $problemeCarte->setStatus('no_card_found');
        }

        $this->entityManager->persist($problemeCarte);
        $this->entityManager->flush();
    }

    public function updateProblemeCarte(ProblemeCarte $problemeCarte): void
    {
        $this->entityManager->flush();
    }

    public function deleteProblemeCarte(ProblemeCarte $problemeCarte): void
    {
        $this->entityManager->remove($problemeCarte);
        $this->entityManager->flush();
    }

    public function getLastRoute(Request $request): string
    {
        $referer = $request->headers->get('referer');
        return $referer ? basename($referer) : '';
    }
}
