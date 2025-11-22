<?php

namespace App\MultiStepBundle\Infrastructure\Symfony\Listener;

use App\Entity\ProblemeCarte;
use App\Entity\Produit;
use App\MultiStepBundle\Application\Enum\StepDataStatus;
use App\MultiStepBundle\Entity\StepData;
use App\MultiStepBundle\Infrastructure\Symfony\Workflow\StepDataWorkflowService;
use App\MultiStepBundle\Infrastructure\Symfony\WorkflowMethodEvent;
use App\MultiStepBundle\Persistence\PersonMicroCesamePersistanceStrategy;
use App\MultiStepBundle\Persistence\Repository\MultistepRepository;
use App\Repository\EntrepriseRepository;
use App\Repository\ProduitRepository;
use App\Service\CartService;
use App\Service\SettingsService;
use App\Service\Workflow\Classes\NotificationService;
use DateMalformedStringException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use DOMDocument;
use DOMException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface as TransportExceptionInterfaceAlias;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class StepDataWorkflowListener
{

    private readonly SessionInterface $session;
    private readonly Produit $produit;

    public function __construct(
        #[Autowire(service: 'state_machine.step_workflow')]
        private readonly WorkflowInterface                    $workflow,
        private readonly MultistepRepository                  $multistepRepository,
        private readonly RequestStack                         $requestStack,
        private readonly SettingsService                      $settingsService,
        private readonly EntityManagerInterface               $entityManager,
        private readonly NotificationService                  $notificationService,
        private readonly PersonMicroCesamePersistanceStrategy $personMicroCesamePersistanceStrategy,
        private readonly StepDataWorkflowService              $stepDataWorkflowService,
        private readonly CartService                          $cart,
        private readonly ProduitRepository                    $produitRepository,
        private readonly RouterInterface $router,
        private readonly EntrepriseRepository $entrepriseRepository,
    )
    {
        $this->session = $this->requestStack->getSession();
        $this->produit = $this->produitRepository->findOneBy(['name' => 'carte']);
    }

    /**
     * @throws TransportExceptionInterfaceAlias
     * @throws DOMException
     * @throws DateMalformedStringException
     */
    #[AsEventListener(event: 'step_data_workflow.applyTransition.before.cerbere_sync')]
    public function onApplyTransitionCerbereSync(WorkflowMethodEvent $event): void
    {
        $demandes = $this->multistepRepository->findBy(['status' => 'tc_temp_ok']);
        if (empty($demandes)) {
            return;
        }

        $filename = sprintf('cerbere_prefecture_%s.xml', date('Ymd_His'));
        $basePath = $_ENV['APP_BASE_PATH'] ?? '/srv/app';
        $xmlContent = $this->convertStepDataToXml($demandes);

        // Génération du fichier CSV
        $directory = $this->settingsService->get('sneas_data_dir');
        $filepath = rtrim($basePath, '/') . '/' . ltrim($directory, '/') . '/output/' . $filename;

        file_put_contents($filepath, $xmlContent);

        // Envoi de l’email à l’équipe SDRI
        $toEmails = explode(',', $this->settingsService->get('sdri_team_emails'));
        $ccEmails = explode(',', $this->settingsService->get('sdri_team_cc_emails'));

        foreach ($toEmails as $to) {
            $this->notificationService->sendTemplatedEmail(
                from: $this->settingsService->get('system_email'),
                to: $to,
                subject: '📄 Nouvelle liste cerbère à traiter (SDRI)',
                cc: $ccEmails[0] ?? null,
                template: 'emails/sdri_cerbere.html.twig',
                templateVars: [
                    'date' => (new DateTime())->format('d/m/Y H:i'),
                    'filename' => $filename,
                    'nbDemandes' => count($demandes),
                ],
                attachmentPath: $filepath,
                attachmentName: 'Fichier_cerbere.xml'
            );
        }

        foreach ($demandes as $demande) {
            $this->workflow->apply($demande, StepDataWorkflowService::TRANSITION_CERBERE_SYNC);
            $this->entityManager->persist($demande);
        }
        $this->entityManager->flush();
    }

    #[AsEventListener(event: 'step_data_workflow.applyTransition.before.validate_investigation')]
    public function onApplyTransitionValidateInvestigation(WorkflowMethodEvent $event): void
    {

        $cesarStepIds = $this->session->get('cesarStepIds', '');
        $event->setResult([
            'type' => 'twig_template',
            'template' => '@MultiStepBundle/transition/before_validate_investigation.html.twig',
            'data' => [
                'cesarStepIds' => $cesarStepIds,
            ]
        ]);

        if (!$this->session->get('step_recheck_validate_investigation', false)) {
            $event->stopPropagation();
        } else {
            $this->session->remove('step_recheck_validate_investigation');
        }
    }

    /**
     * @throws TransportExceptionInterfaceAlias
     */
    #[AsEventListener(event: 'step_data_workflow.applyTransition.before.launch_preliminary_investigation')]
    public function onApplyTransitionLaunchingInvestigation(WorkflowMethodEvent $event): void
    {
        $demandes = $this->multistepRepository->findBy(['status' => 'microcesame']);
        if (empty($demandes)) {
            return;
        }

        $filename = 'CRIBLAGE_SNEAS.csv';
        $basePath = $_ENV['APP_BASE_PATH'] ?? '/srv/app';
        $updatedPath = str_replace($basePath, '', $this->settingsService->get('sneas_data_dir'));
        $csvContent = file_get_contents(rtrim($basePath, '/') .'/'. ltrim($updatedPath . '/' .$filename, '/'));
        $successfulDemands = [];
        foreach ($demandes as $demande) {
            if ($demande->getCesarStepId() && $demande->getCesarStepLine()) {
                $csvContent .= $demande->getCesarStepLine() . "\n";

                // Notifier chaque utilisateur
                $this->notificationService->sendAppNotification(
                    $demande->getUser(),
                    "Votre demande [{$demande->getStepNumber()}] a été transmise à l’équipe SDRI pour investigation."
                );
            }
            $successfulDemands[] = $demande;
        }

        // Génération du fichier CSV
        // $filename = 'criblage_sdri_' . date('Ymd_His') . '.csv';
        $filepath = rtrim($basePath, '/') . '/' . ltrim($updatedPath, '/') . '/output/' . $filename;

        file_put_contents($filepath, $csvContent);

        // Envoi de l’email à l’équipe SDRI
        $toEmails = explode(',', $this->settingsService->get('sdri_team_emails'));
        $ccEmails = explode(',', $this->settingsService->get('sdri_team_cc_emails'));

        foreach ($toEmails as $to) {
            $this->notificationService->sendTemplatedEmail(
                from: $this->settingsService->get('system_email'),
                to: $to,
                subject: '📄 Nouvelle liste de criblage à traiter (SDRI)',
                cc: $ccEmails[0] ?? null,
                template: 'emails/sdri_criblage.html.twig',
                templateVars: [
                    'date' => (new DateTime())->format('d/m/Y H:i'),
                    'filename' => $filename,
                    'nbDemandes' => count($demandes),
                ],
                attachmentPath: $filepath,
                attachmentName: 'Liste_Criblage_SNEAS.csv'
            );
        }


        foreach ($successfulDemands as $demande) {
            $this->workflow->apply($demande, StepDataWorkflowService::TRANSITION_LAUNCH_PRELIMINARY_INVESTIGATION);
            $this->entityManager->persist($demande);
        }
        $this->entityManager->flush();
        $event->stopPropagation();
    }

    #[AsEventListener(event: 'step_data_workflow.applyTransition.before.request_more_info')]
    #[AsEventListener(event: 'step_data_workflow.applyTransition.before.needs_clarification')]
    public function onApplyTransitionBeforeRequestMoreInfo(WorkflowMethodEvent $event): void
    {
        /** @var StepData $demande */
        $demande = $event->getParams()['demande'];
        if ($demande) {
            $existingNames = $this->multistepRepository->findAllStepNamesForUser($demande->getUser());
            $event->setResult([
                'type' => 'twig_template',
                'template' => '@MultiStepBundle/transition/recheck.html.twig',
                'data' => [
                    'all_data' => $demande->getData(),
                    'step_id' => $demande->getStepId(),
                    'step_number' => $demande->getStepNumber(),
                    'existingNames' => $existingNames
                ]
            ]);
            if (!$this->session->get('step_recheck_review_information_submitted_' . $demande->getStepId(), false)) {
                $event->stopPropagation();
            }
        }
    }

    #[AsEventListener(event: 'step_data_workflow.applyTransition.before.reject')]
    public function onApplyTransitionBeforeReject(WorkflowMethodEvent $event): void
    {
        $rejectReason = $this->session->get('reject_reason', '');

        /** @var StepData $demande */
        $demande = $event->getParams()['demande'];

        $event->setResult([
            'type' => 'twig_template',
            'template' => '@MultiStepBundle/transition/before_reject.html.twig',
            'data' => [
                'reject_reason' => $rejectReason,
                'demande' => $demande->getStepId(),
            ]
        ]);

        if (!$this->session->get('step_reject_reason', false)) {
            $event->stopPropagation();
        } else {
            $this->session->remove('step_reject_reason');
        }
    }

    /**
     * @throws DateMalformedStringException
     * @throws TransportExceptionInterfaceAlias
     */
    #[AsEventListener(event: 'workflow.saveData.after')]
    public function onApplyTransitionAfterPrepareTechnicalRecord(WorkflowMethodEvent $event): void
    {
        $params = $event->getParams();
        if (array_key_exists('demande', $params)) {
            /** @var StepData $demande */
            $demande = $params['demande'];

            if ($demande)
                $this->stepDataWorkflowService->advanceToDeposit($demande);
                $this->stepDataWorkflowService->awaitReference($demande);
        }
        if (array_key_exists('stepId', $params)) {
            if (in_array($params['stepId'], ['person_step_six', 'person_step_five'])) {
                if (array_key_exists('entity', $event->getResult()??[])) {
                    $demande = $event->getResult()['entity'];
                    $this->stepDataWorkflowService->advanceToDeposit($demande);
                    $this->stepDataWorkflowService->awaitReference($demande);
                }
            }
        }
    }
    /**
     * @throws DateMalformedStringException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[AsEventListener(event: 'step_data_workflow.applyTransition.before.prepare_technical_record')]
    public function onApplyTransitionBeforePrepareTechnicalRecord(WorkflowMethodEvent $event): void
    {
        /** @var StepData $demande */
        $demande = $event->getParams()['demande'];

        // 1. Generate CEZAR line and ID
        $cezarLine = $this->generateCezarLine($demande);
        $cezarId = explode(';', $cezarLine)[0]; // First element is the unique CEZAR ID

        // 2. Save values to entity
        $demande->setCesarStepId($cezarId);
        $demande->setCesarStepLine($cezarLine);

        if ($demande->getInternalData('person_step_one', 'access_duration') !== 'temporaire') {
            $this->personMicroCesamePersistanceStrategy->saveData($demande->getStepNumber(), $demande->getData());
        }

        // 3. Persist changes
        $this->entityManager->persist($demande);
        $this->entityManager->flush();

    }

    public function generateCezarLine(StepData $stepData): string
    {
        // ✅ Return early if line already exists
        if ($stepData->getCesarStepLine()) {
            return $stepData->getCesarStepLine();
        }

        // 1. NUMERO IDENTIFICATION CEZAR
        $year = date('Y');
        $sequence = str_pad((int) $this->settingsService->get('cesar_sequence', 1), 4, '0', STR_PAD_LEFT);
        $numeroCezar = "FLUXEL-$year-$sequence";

        // 2. TERMINAL (from access_locations)
        $accessLocations = $stepData->getInternalData('person_step_one', 'access_locations');
        $accessLocationsArray = is_array($accessLocations) ? $accessLocations : json_decode($accessLocations, true) ?? [];
        $terminal = '';
        if (in_array('lavera', $accessLocationsArray)) {
            $terminal = 'TERMINAL PETROCHIMIQUE DE LAVERA';
        } elseif (in_array('fos', $accessLocationsArray)) {
            $terminal = 'TERMINAL DE FOS';
        }

        // 4. NOM SOCIETE
        $company = $stepData->getInternalData('person_step_one', 'company_name');

        // 9. Fonction (or fallback)
        $function = strtoupper($stepData->getInternalData('person_step_two', 'employee_function') ?: 'VISITE');

        // 11–18: Identity & Birth
        $lastName      = strtoupper($stepData->getInternalData('person_step_two', 'employee_last_name'));
        $firstName     = strtoupper($stepData->getInternalData('person_step_two', 'employee_first_name'));
        $gender        = strtoupper(substr($stepData->getInternalData('person_step_two', 'gender'), 0, 1));
        $birthdate     = date('d/m/Y', strtotime($stepData->getInternalData('person_step_two', 'employee_birthdate')));
        $birthplace    = strtoupper($stepData->getInternalData('person_step_two', 'employee_birthplace'));
        $birthCountry  = strtoupper($stepData->getInternalData('person_step_two', 'country'));
        $birthPostCode = ($birthCountry !== 'FRANCE') ? '999' : $stepData->getInternalData('person_step_two', 'employee_birth_postale_code');

        // 21–22: Pièce d'identité
        $numeroCni = $stepData->getInternalData('person_step_two', 'numero_cni');
        if ($numeroCni) {
            $identityType = 'CNI';
            $identityNumber = $numeroCni;
        } else {
            $idCardPath = $stepData->getInternalData('person_step_five', 'id_card');
            $passportPath = $stepData->getInternalData('person_step_five', 'passport');
            $fileUsed = $idCardPath ?: $passportPath;
            $identityType = $idCardPath ? 'CNI' : 'PPT';
            $identityNumber = $fileUsed ? basename($fileUsed) : '';
        }

        // Assemble CEZAR line
        $cezarLine = [
            $numeroCezar,         // 1. Numéro CEZAR
            $terminal,            // 2. Terminal
            "",                   // 3. NE PAS REMPLIR
            $company,             // 4. Société
            "",                   // 5. NE PAS REMPLIR
            "AUTORISATION_ACCES", // 6. Type d'autorisation
            "", "",               // 7-8. NE PAS REMPLIR
            $function,            // 9. Fonction
            "",                   // 10. NE PAS REMPLIR
            $lastName,            // 11. NOM
            "",                   // 12. NE PAS REMPLIR
            $firstName,           // 13. PRÉNOM
            $gender,              // 14. Sexe
            $birthdate,           // 15. Date naissance
            $birthplace,          // 16. Ville naissance
            $birthPostCode,       // 17. Code postal naissance
            $birthCountry,        // 18. Pays naissance
            "", "",               // 19-20. NE PAS REMPLIR
            $identityType,        // 21. Type de pièce
            $identityNumber,      // 22. Numéro de pièce
            "", "", "", "",       // 23–26. NE PAS REMPLIR
            "X"                   // 27. Fin de ligne
        ];

        $this->settingsService->increment('cesar_sequence');

        return implode(';', $cezarLine);
    }

    /**
     * @param array<StepData> $stepDatas
     * @return string
     * @throws \DOMException
     */


    /**
     * @param array<StepData> $stepDatas
     * @return string
     * @throws DateMalformedStringException
     */
    function convertStepDataToXml(array $stepDatas): string
    {

        $birthCountry = $stepTwo['country'] ?? 'France';
        $addressCountry = $stepTwo['country'] ?? 'France';

        $codePaysNaissance = strtoupper(substr($birthCountry, 0, 2));
        $codePaysAdresse = strtoupper(substr($addressCountry, 0, 2));

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $root = $xml->createElement("personnes");
        $root->setAttributeNS("http://www.w3.org/2001/XMLSchema-instance", "xsi:noNamespaceSchemaLocation", "import_personnes.xsd");

        foreach ($stepDatas as $stepData) {
            $data = $stepData->getData();
            $stepTwo = $data['person_step_two'] ?? [];
            $personne = $xml->createElement("personne");

            $this->notificationService->sendAppNotification(
                $stepData->getUser(),
                "Votre demande [{$stepData->getStepNumber()}] a été transmise à la prefecture pour enquête."
            );

            // Données d'identité
            $personne->appendChild($xml->createElement("libelleTitre", strtoupper($stepTwo['gender'] ?? '')));
            $personne->appendChild($xml->createElement("nom", $stepTwo['employee_last_name'] ?? ''));
            $personne->appendChild($xml->createElement("prenom", $stepTwo['employee_first_name'] ?? ''));
            $personne->appendChild($xml->createElement("dateNaissance", (new DateTime($stepTwo['employee_birthdate'] ?? ''))->format('d/m/Y')));
            $personne->appendChild($xml->createElement("codePaysNaissance", $codePaysNaissance));
            $personne->appendChild($xml->createElement("lieuNaissance", $stepTwo['employee_birthplace'] ?? ''));
            $personne->appendChild($xml->createElement("cpNaissance", $stepTwo['employee_birth_postale_code'] ?? ''));
            $personne->appendChild($xml->createElement("arrondissementNaissance", $stepTwo['employee_birth_district'] ?? ''));
            $personne->appendChild($xml->createElement("nationalite", strtoupper(substr($stepTwo['nationality'] ?? '', 0, 1))));

            // Adresse
            $adresse = $xml->createElement("adresse");
            $adresse->appendChild($xml->createElement("pointGeo", "0"));
            $adresse->appendChild($xml->createElement("noEtVoie", $stepTwo['section_employee_address'] ?? ''));
            $adresse->appendChild($xml->createElement("distribution", ''));
            $adresse->appendChild($xml->createElement("codePostal", $stepTwo['postal_code'] ?? ''));
            $adresse->appendChild($xml->createElement("ville", $stepTwo['city'] ?? ''));
            $personne->appendChild($adresse);

            $personne->appendChild($xml->createElement("codePaysAdresse", $codePaysAdresse));

            // Coordonnées
            $coordonnees = $xml->createElement("coordonnees");
            $coordonnees->appendChild($xml->createElement("tel", $stepTwo['employee_phone'] ?? ''));
            $coordonnees->appendChild($xml->createElement("email", $stepTwo['employee_email'] ?? ''));
            $personne->appendChild($coordonnees);

            // État civil
            $personne->appendChild($xml->createElement("epouseDePersonneNom", $stepTwo['maiden_name'] ?? ''));
            $personne->appendChild($xml->createElement("sexe", strtoupper(substr($stepTwo['gender'] ?? '', 0, 1))));
            $personne->appendChild($xml->createElement("identifCompl", $stepTwo['social_security_number'] ?? ''));
            $personne->appendChild($xml->createElement("enActivite", "A"));

            $personne->appendChild($xml->createElement("nomPere", $stepTwo['father_name'] ?? ''));
            $personne->appendChild($xml->createElement("prenomPere", $stepTwo['father_first_name'] ?? ''));
            $personne->appendChild($xml->createElement("nomMere", $stepTwo['mother_maiden_name'] ?? ''));
            $personne->appendChild($xml->createElement("prenomMere", $stepTwo['mother_first_name'] ?? ''));

            $root->appendChild($personne);
        }

        $xml->appendChild($root);

        return $xml->saveXML();
    }


    /**
     * @throws NonUniqueResultException
     */
    #[AsEventListener(event: 'step_data_workflow.applyTransition.after.cerbere_return')]
    public function onApplyTransitionAfterCerbereReturn(WorkflowMethodEvent $event): void
    {
        /** @var StepData $demande */
        $demande = $event->getParams()['demande'] ?? null;

        if (!$demande) {
            return;
        }
        $siret = $demande->getInternalData('person_step_one', 'siret');
        $entreprise = $this->entrepriseRepository->findOneBySiret($siret);
        if ($entreprise) {
            if ($entreprise->isGratuit()) {
                $demande->setStatus(StepDataStatus::PAID);
                $this->entityManager->persist($demande);
                $this->entityManager->flush();
            }
        }

    }

    #[AsEventListener(event: 'step_data_workflow.applyTransition.after.request_payment')]
    public function onApplyTransitionAfterRequestPayment(WorkflowMethodEvent $event): void
    {
        /** @var StepData $demande */
        $demande = $event->getParams()['demande'] ?? null;

        if (!$demande) {
            return;
        }

        $id = $this->produit->getId();
        $stepId = $demande->getStepId();
        $stepType = $demande->getStepType();

        $this->cart->add($id, $stepId, $stepType);
    }

    #[AsEventListener(event: 'step_data_workflow.applyTransition.after.reedit_card')]
    public function onApplyTransitionAfterReeditCard(WorkflowMethodEvent $event): void
    {
        /** @var StepData $demande */
        $demande = $event->getParams()['demande'] ?? null;

        if (!$demande) {
            return;
        }

        $id = $this->produit->getId();
        $stepId = $demande->getStepId();
        $stepType = $demande->getStepType();

        $this->cart->add($id, $stepId, $stepType);
    }

    #[AsEventListener(event: 'step_data_workflow.applyTransition.before.edit_card')]
    public function onBeforeDeliverCardTransitionDeliverCard(WorkflowMethodEvent $event): void
    {
        /** @var StepData|null $demande */
        $demande = $event->getParams()['demande'] ?? null;
        if (!$demande) return;

        $pickup    = $demande->getInternalData('person_step_six', 'card_place');
        $locations = $demande->getInternalData('person_step_one', 'access_locations');
        $locations = \is_array($locations) ? $locations : [];

        // Auto-sélection si un seul site
        if (!$pickup && \count($locations) === 1) {
            $demande->setInternalData('person_step_six', 'card_place', $locations[0]);
            $this->entityManager->flush();
            return;
        }

        if (!$pickup) {
            $url = $this->router->generate('person_access_card_place', ['id' => $demande->getStepId()]);
            // 👉 Instruction JSON pour le front
            $event->setResult([
                'type' => 'redirect',
                'action'  => 'open_form',
                'section' => 'person_step_six',
                'url'     => $url,
                'message' => 'Veuillez sélectionner un lieu de retrait.',
            ]);
            $event->stopPropagation();
            if (method_exists($event, 'halt')) $event->halt('card_place_required');
            return;
        }

        // (optionnel) cohérence
        if ($locations && !\in_array($pickup, $locations, true)) {
            $event->setResult([
                'type' => 'json_response',
                'data' => [
                    'action'  => 'open_form',
                    'section' => 'person_step_six',
                    'url'     => $this->router->generate('person_access_card_place', ['id' => $demande->getStepId()]),
                    'message' => 'Le lieu de retrait doit appartenir aux sites demandés.',
                ],
            ]);
            $event->stopPropagation();
            if (method_exists($event, 'halt')) $event->halt('card_place_mismatch');
        }
    }

    #[AsEventListener(event: 'step_data_workflow.applyTransition.after.request_payment')]
    #[AsEventListener(event: 'step_data_workflow.applyTransition.after.confirm_payment')]
    #[AsEventListener(event: 'step_data_workflow.applyTransition.after.edit_card')]
    #[AsEventListener(event: 'step_data_workflow.applyTransition.after.deliver_card')]
    public function onAfterPaymentTransitions(WorkflowMethodEvent $event): void
    {
        /** @var StepData $demande */
        $demande = $event->getParams()['demande'] ?? null;
        if (!$demande) {
            return;
        }

        $email = $demande->getInternalData('person_step_two', 'employee_email');
        if (!$email) {
            return;
        }

        $problemeCarte = $this->entityManager
            ->getRepository(ProblemeCarte::class)
            ->findOneBy(['email' => $email]);

        if ($problemeCarte) {
            $problemeCarte->setStatus($demande->getStatus());
            $this->entityManager->persist($problemeCarte);
            $this->entityManager->flush();
        }
    }
}