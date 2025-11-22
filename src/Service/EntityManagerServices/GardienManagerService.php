<?php

namespace App\Service\EntityManagerServices;

use App\Entity\DemandeTitreCirculation;
use App\Entity\Entreprise;
use App\Entity\MailAppli;
use App\Entity\ProblemeCarte;
use App\Entity\User;
use App\MultiStepBundle\Application\Enum\StepDataStatus;
use App\MultiStepBundle\Entity\StepData;
use App\MultiStepBundle\Persistence\Repository\MultistepRepository;
use App\Service\Workflow\Interfaces\NotificationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use DateTime;

class GardienManagerService
{

    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly NotificationServiceInterface $notificationService)
    {
    }

    public function getTableauData(MultistepRepository $multistepRepository): array
    {
        $problemeCarteRepository = $this->entityManager->getRepository(ProblemeCarte::class);
        $entrepriseRepository = $this->entityManager->getRepository(Entreprise::class);
        $userRepository = $this->entityManager->getRepository(User::class);

        $statusList = [StepDataStatus::CARD_EDITED, StepDataStatus::CARD_DELIVERED];
        $demandes = $multistepRepository->findByStatuses($statusList);

        return [
            'problemeCarte' => $problemeCarteRepository->findAll(),
            'Entreprise' => $entrepriseRepository->findAll(),
            'EntrepriseCount' => $entrepriseRepository->count([]),
            'user' => $userRepository->findAll(),
            'userCount' => $userRepository->count([]),
            'demandes' => $demandes,
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function handleCommentaire(int $id, string $parametre, ?string $comment, $currentUser): string
    {
        $demandeRepository = $this->entityManager->getRepository(DemandeTitreCirculation::class);
        $mailAppliRepository = $this->entityManager->getRepository(MailAppli::class);

        $demande = $demandeRepository->find($id);
        $mailAppli = $mailAppliRepository->find(1);

        $timestamp = new DateTime();
        $userEmail = $currentUser->getEmail();

        $comment .= "<br>{$timestamp->format('Y/m/d - H:i')}<br>" . $userEmail;
        $demande->setCommentaire($comment);

        if ($parametre === 'delivrer' && $demande->getStatus() !== DemandeTitreCirculation::STATUS_CARD_DELIVERED) {
            $demande->setStatus(DemandeTitreCirculation::STATUS_CARD_DELIVERED);
            $this->notificationService->sendTemplatedEmail(
                from: $mailAppli->getEmail(),
                to: $demande->getUser()->getEmail(),
                subject: 'FLUXEL : Votre demande de titre de circulation vous a été délivrer',
                template: 'email_status_titre/delivrer.html.twig'
            );
        }

        $this->entityManager->flush();

        return $this->determineRedirectRoute(basename($currentUser->getReferer() ?? ''));
    }

    private function determineRedirectRoute(string $lastRoute): string
    {
        return $lastRoute === 'demandesdri' ? 'app_demande_sdri' : 'app_gardien_tableau';
    }
}
