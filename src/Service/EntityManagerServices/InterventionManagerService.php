<?php

namespace App\Service\EntityManagerServices;

use App\Entity\DemandeTitreCirculation;
use App\Entity\Entreprise;
use App\Entity\Intervention;
use App\Entity\User;
use App\Entity\MailAppli;
use App\Form\InterventionType;
use App\Service\Workflow\Interfaces\NotificationServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\FormFactoryInterface;

class InterventionManagerService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NotificationServiceInterface $notificationService,
        private readonly FormFactoryInterface $formFactory,
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function getAll(): array
    {
        return $this->entityManager->getRepository(Intervention::class)->findAll();
    }

    public function getUserLatestRequest(User $user)
    {
        return $user->getDemandes()->last();
    }

    public function saveIntervention(Intervention $intervention, $demandeTitreCirculation): void
    {
        $demandeTitreCirculation->setIntervention($intervention);
        $intervention->setSubmited(true);

        $this->entityManager->persist($intervention);
        $this->entityManager->flush();

        $this->entityManager->persist($demandeTitreCirculation);
        $this->entityManager->flush();
    }

    public function delete(Intervention $intervention): void
    {
        $this->entityManager->remove($intervention);
        $this->entityManager->flush();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailIntervention(Request $request, User $user): void
    {
        $mailAppli = $this->entityManager->getRepository(MailAppli::class)->find(1);
        $this->notificationService->sendTemplatedEmail(
            from: $mailAppli->getEmail(),
            to: 'benjamin.migliani@fluxel.fr',
            subject: 'Je ne trouve pas mon entreprise dans la liste',
            template: 'email_entreprise_manquante/entrepriseManquante.html.twig',
            templateVars: [
                'link' => $this->generateMissingCompanyLink($request, $user),
                'userEmail' => $user->getEmail(),
            ]
        );
    }

    private function generateMissingCompanyLink(Request $request, User $user): string
    {
        $params = array_merge($request->request->all(), ['userEmail' => $user->getEmail()]);

        return $this->urlGenerator->generate('app_entreprise_missing', $params, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function createForm(Intervention $intervention, $demandeTitreCirculation): FormInterface
    {
        $entreprises = $this->entityManager->getRepository(Entreprise::class)->findAll();

        return $this->formFactory->create(InterventionType::class, $intervention, [
            'entreprises' => $entreprises,
            'entrepriseStockee' => $demandeTitreCirculation->getEntreprise(),
        ]);
    }

    public function prepareFormData(Intervention $intervention, DemandeTitreCirculation $demandeTitreCirculation, FormInterface $form): array
    {
        return [
            'form' => $form->createView(),
            'intervention' => $intervention,
            'demande' => $demandeTitreCirculation,
            'etatCivil' => $demandeTitreCirculation->getEtatCivil(),
            'filiation' => $demandeTitreCirculation->getFiliation(),
            'adresse' => $demandeTitreCirculation->getAdresse(),
            'infoComplementaire' => $demandeTitreCirculation->getInfocomplementaire(),
            'documentPersonnel' => $demandeTitreCirculation->getDocpersonnel(),
            'documentProfessionnel' => $demandeTitreCirculation->getDocumentprofessionnel(),
        ];
    }

    public function determineNextRoute($demandeTitreCirculation): array
    {
        $etatCivil = $demandeTitreCirculation->getEtatCivil();

        if ($etatCivil !== null) {
            return ['route' => 'app_etat_civil_edit', 'params' => ['id' => $etatCivil->getId()]];
        }

        return ['route' => 'app_etat_civil_new', 'params' => []];
    }
}
