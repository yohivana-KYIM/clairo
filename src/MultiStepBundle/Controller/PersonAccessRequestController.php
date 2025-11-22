<?php

namespace App\MultiStepBundle\Controller;

use App\Entity\User;
use App\MultiStepBundle\Application\PersonAccessWorkflowService;
use App\MultiStepBundle\Domain\Person\AbstractPersonStep;
use App\MultiStepBundle\Entity\StepData;
use App\MultiStepBundle\Infrastructure\Symfony\Workflow\StepDataWorkflowService;
use App\MultiStepBundle\Persistence\Repository\MultistepRepository;
use App\MultiStepBundle\Persistence\Repository\View\PersonAdminStepViewRepository;
use App\MultiStepBundle\Persistence\Repository\View\PersonGardienStepViewRepository;
use App\MultiStepBundle\Persistence\Repository\View\PersonRefsecuStepViewRepository;
use App\MultiStepBundle\Persistence\Repository\View\PersonSdriStepViewRepository;
use App\MultiStepBundle\Persistence\Repository\View\PersonUserStepViewRepository;
use App\Service\NameGuesser;
use App\Service\SettingsService;
use App\Service\Workflow\Classes\NotificationService;
use DateMalformedStringException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use UnexpectedValueException;
use ZipArchive;

class PersonAccessRequestController extends AbstractController
{
    private readonly SessionInterface $session;
    public function __construct(
        private readonly MultistepRepository $multistepRepository,
        private readonly PersonAccessWorkflowService $workflowService,
        private readonly UrlMatcherInterface $urlMatcher,
        private readonly StepDataWorkflowService $stepDataWorkflowService,
        private readonly RouterInterface $router,
        private readonly NotificationService $notificationService,
        private readonly SettingsService $settingsService,
        private readonly RequestStack $requestStack,
        private readonly PersonUserStepViewRepository $personUserStepViewRepository,
        private readonly PersonAdminStepViewRepository $personAdminStepViewRepository,
        private readonly PersonRefsecuStepViewRepository $personRefsecuStepViewRepository,
        private readonly PersonSdriStepViewRepository $personSdriStepViewRepository,
        private readonly PersonGardienStepViewRepository $personGardienStepViewRepository,
        private readonly NameGuesser $nameGuesser,
        private readonly TranslatorInterface $translator,
    )
    {
        $this->session = $this->requestStack->getSession();
    }

    public function handleFromNull(Request $request, ?StepData $stepData = null): Response
    {
        $this->workflowService->resetWorkflow();
        return $this->handle($request, $stepData);
    }

    public function handle(Request $request, ?StepData $stepData = null): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        if (!$this->stepDataWorkflowService->isWritable($stepData, $user)) return $this->review($stepData);
        $existingNames = $this->multistepRepository->findAllStepNamesForUser($user);
        $currentStep = $this->workflowService->getCurrentStep();

        $data = $this->workflowService->loadData();
        $errors = [];
        if (method_exists($currentStep, 'checkStepDatas')) {
            $requestDatas = array_values($request->files->all());
            if ($requestDatas)
                $errors = $currentStep->checkStepDatas(reset($requestDatas));
        }

        $form_data = $currentStep->processLoadedData($data);

        if ($currentStep instanceof AbstractPersonStep) {
            $currentStep->setPreviousFormData($data);
        }

        $form = $this->createForm($currentStep->getFormType(), $form_data);
        $form->handleRequest($request);

        if (empty($errors)) {
            if ($form->isSubmitted() && $form->isValid()) {
                $currentStep->process($form);
                $this->workflowService->saveData($currentStep->getData());
                if ($this->workflowService->isComplete()) {
                    if ($stepData) {
                        $this->stepDataWorkflowService->advanceToDeposit($stepData);
                    }
                    return $this->review($stepData);
                }
                if ($this->workflowService->advance()) {
                    $this->stepDataWorkflowService->initIfNull($stepData);
                }

                return $this->handle($request, $stepData);
            }
        } else {
            foreach ($errors as $key => $errorgroup) {
                foreach ($errorgroup as $error) {
                    $this->addFlash('error', sprintf('%s: %s', $this->translator->trans($key), $error));
                }
            }
        }

        return $this->render('@MultiStepBundle/step.html.twig', [
            'form' => $form->createView(),
            'current_step' => $currentStep->getName(),
            'step_trail' => $this->workflowService->generateStepTrail(),
            'buttons' => $this->workflowService->generateButtons($stepData),
            'step_entity' => 'les personnes/salariés',
            'step_asset' => $currentStep->getCustomScriptUrl(),
            'data' => $data,
            'step_id' => $stepData?->getStepId() ?? 0,
            'step_number' => $stepData?->getStepNumber() ?? $currentStep->getId(),
            'existingNames' => $existingNames,
            'readonly_mode' => !$this->stepDataWorkflowService->isWritable($stepData, $user),
            'errors' => $errors,
        ]);
    }

    public function goBack(): Response
    {
        $this->workflowService->goBack();
        return $this->redirectToRoute('person_access_request');
    }

    public function review(?StepData $stepData = null): Response
    {
        $allData = $this->workflowService->getAllData(true);
        /** @var User $user */
        $user = $this->getUser();
        $existingStepNames = $this->multistepRepository->findAllStepNamesForUser($user);

        $routeParams = [];
        if ($stepData !== null) {
            $routeParams['id'] = $stepData->getStepId();
        }
        return $this->render('@MultiStepBundle/review.html.twig', [
            'all_data' => $allData,
            'review_back_link' => $this->router->generate('person_access_previous', $routeParams),
            'review_persist_link' => $this->router->generate('person_access_persist', $routeParams),
            'review_edit_link' => $this->router->generate('person_access_request', $routeParams),
            'step_id' => $stepData?->getStepId() ?? 0,
            'step_number' => $stepData?->getStepNumber() ?? '',
            'existingNames' => $existingStepNames,
            'is_writable' => $this->stepDataWorkflowService->isWritable($stepData, $user),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function persist(Request $request, ?StepData $stepData = null): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        try {
            $currentStep = $this->workflowService->getCurrentStep();

            $finalName = $request->request->get('finalName');
            $ending = $request->request->getBoolean('ending');
            $request->request->remove('finalName');
            $request->request->remove('confirmOverwrite');
            $request->request->remove('ending');

            $requestDatas = $request->request->all();
            $filesData = $request->files->all();

            if ($ending & $stepData !== null) {
                if ($stepData->getStepNumber() === $finalName) {
                    if (empty($stepData->getMicrocesameId())) {
                        $personDatas = $stepData->getData() ?? [];
                        $this->workflowService->saveData($personDatas, 'microcesame.person', $finalName);
                    }
                    $this->stepDataWorkflowService->awaitReference($stepData);
                    return $this->redirectToRoute('person_access_list');
                }
            }

            foreach ($filesData as $fileKey => $fileArray) {
                foreach ($fileArray as $key => $value) {
                    if ($value instanceof UploadedFile) {
                        $currentStep->handleFileUpload($key, $value, $requestDatas[$fileKey]);
                    }
                }
            }
            $requestDatas = array_shift($requestDatas);
            unset($requestDatas['_token']);

            $this->workflowService->saveData(($requestDatas ?? []), 'single_table', $finalName);

            $this->notificationService->sendAppNotification($user, 'Votre demande a été bien enregistré');

            if ($ending) {
                if ($stepData)
                    $this->stepDataWorkflowService->advanceToDeposit($stepData);
                $this->workflowService->resetWorkflow();
            }

            // Redirect back to the same step for confirmation
            return $this->redirectToRoute('person_access_list');
        } catch (Exception $e) {

            $this->notificationService->sendAppNotification($user, 'Une erreur est survenue' . $e->getMessage());
            // Redirect back to the same step for retry
            return $this->redirectToRoute('person_access_list');
        }
    }

    public function list(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);

        // 1) Récupère les listes sans merges successifs
        $chunks = [];

        if ($this->isGranted('ROLE_ADMIN')) {
            $chunks[] = $this->personAdminStepViewRepository->findForAdminSorted();
        }
        if ($this->isGranted('ROLE_SDRI')) {
            $chunks[] = $this->personSdriStepViewRepository->findForSdriSorted();
        }
        if ($this->isGranted('ROLE_REFSECU')) {
            $chunks[] = $this->personRefsecuStepViewRepository->findForRefsecuSorted(
                $user->getId(),
                $user->getEntreprise()?->getSiret() ?? '',
                $user->getEmail()
            );
        }
        if ($this->isGranted('ROLE_GARDIEN')) {
            $chunks[] = $this->personGardienStepViewRepository->findForGardienSorted();
        }
        if ($this->isGranted('ROLE_USER')) {
            $chunks[] = $this->personUserStepViewRepository->findStepsForUserSorted($user->getId());
        }

        // 2) Déduplique par step_number en conservant l'ordre d'apparition
        $orderedUniqueSteps = [];
        $seenNumbers = [];

        foreach ($chunks as $list) {
            foreach ($list as $stepView) {
                $num = $stepView->getStepNumber();
                if (!isset($seenNumbers[$num])) {
                    $seenNumbers[$num] = true;
                    $orderedUniqueSteps[] = $stepView;
                }
            }
        }

        if (!$orderedUniqueSteps) {
            return $this->render('@MultiStepBundle/person/list.html.twig', [
                'steps'   => [],
                'actions' => [],
                'writer'  => $this->stepDataWorkflowService,
            ]);
        }

        // 3) Batch load des StepData pour éviter le N+1
        $stepIds = [];
        foreach ($orderedUniqueSteps as $sv) {
            $stepIds[] = $sv->getStepId();
        }
        $stepIds = array_values(array_unique($stepIds));

        $stepDataList = $this->multistepRepository->findBy(['stepId' => $stepIds]);
        $stepDataById = [];
        foreach ($stepDataList as $sd) {
            $stepDataById[$sd->getStepId()] = $sd;
        }

        // 4) Génère les actions (sans unset, on filtre à l'ajout)
        $stepsActions = [];

        foreach ($orderedUniqueSteps as $sv) {
            $sid = $sv->getStepId();
            if (!isset($stepDataById[$sid])) {
                continue;
            }

            $stepData = $stepDataById[$sid];
            $transitions = $this->stepDataWorkflowService->getAvailableTransitions($stepData);

            $actions = [];
            foreach ($transitions as $transition) {
                $tName = $transition->getName();
                if ($tName === 'reset_investigation' || $tName === 'reedit_card') {
                    continue;
                }
                $actions[$tName] = [
                    'label' => $tName,
                    'class' => 'btn btn-cleo',
                    'route' => 'person_access_transition',
                    'params' => [
                        'id' => $stepData->getStepId(),
                        'transition' => $tName,
                    ],
                    'translationKey' => $tName,
                ];
            }

            $stepsActions[$sid] = $actions;
        }

        return $this->render('@MultiStepBundle/person/list.html.twig', [
            'steps'   => $orderedUniqueSteps,
            'actions' => $stepsActions,
            'writer' => $this->stepDataWorkflowService,
        ]);
    }

    public function show(StepData $step): Response
    {
        $allData = $step->getData();
        $user = $this->getUser();
        /** @var User  $user */
        $existingNames = $this->multistepRepository->findAllStepNamesForUser($user);
        $routeParams = [];
        $routeParams['id'] = $step->getStepId();

        return $this->render('@MultiStepBundle/review.html.twig', [
            'all_data' => $allData,
            'review_back_link' => 'person_access_previous',
            'review_persist_link' => $this->router->generate('person_access_persist', $routeParams),
            'review_edit_link' => 'person_access_request',
            'step_id' => $step->getStepId(),
            'step_number' => $step->getStepNumber(),
            'existingNames' => $existingNames,
            'hide_buttons' => true,
            'is_writable' => $this->stepDataWorkflowService->isWritable($step, $user),
        ]);
    }

    public function downloadByWord(StepData $step): Response
    {
        $templatePath = $this->getParameter('kernel.project_dir') . '/private/word/fluxel_r17.docx';
        $tempDir = sys_get_temp_dir() . '/word_' . uniqid();
        $outputPath = $tempDir . '/fluxel_filled.docx';

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Dézipper le modèle Word
        $zip = new ZipArchive();
        if ($zip->open($templatePath) !== true) {
            throw new RuntimeException("Impossible d’ouvrir le modèle Word");
        }
        $zip->extractTo($tempDir);
        $zip->close();

        $docXmlPath = $tempDir . '/word/document.xml';
        $xml = file_get_contents($docXmlPath);

        // Récupérer les données
        $data = $step->toFluxelTemplateArray(
            $this->settingsService->get('sdri_receive_refsec_email'),
            $this->settingsService->get('sdri_team_emails'),
            $this->settingsService->get('sdri_team_cc_emails')
        );

        // Labels des accès
        $accessLabels = [
            'fos'    => 'Port pétrolier de Fos-sur-Mer',
            'lavera' => 'Port pétrolier de Lavéra',
            'siege'  => 'Siège social FLUXEL à Lavéra',
        ];

        $sdriToEmails = $sdriCcEmails = null;
        if ($this->settingsService->get('sdri_receive_refsec_email')) {
            $sdriToEmails = explode(',', $this->settingsService->get('sdri_team_emails'));
            $sdriCcEmails = explode(',', $this->settingsService->get('sdri_team_cc_emails'));
            $sdriToEmails = reset($sdriToEmails);
            $sdriCcEmails = reset($sdriCcEmails);
        }

        // 🔑 Mapping entre les clés de ton DOCX et les données de ton tableau
        $mapping = [
            // Employeur
            'raison_sociale'      => $data['employer']['company_name'] ?? '',
            'siren'               => $data['employer']['siren'] ?? '',
            'siret'               => $data['employer']['siret'] ?? '',
            'codeApe'             => $data['employer']['naf'] ?? '',
            'num_tva'             => $data['employer']['vat_number'] ?? '',
            'adresse'             => $data['employer']['address'] ?? '',
            'adresse '             => $data['employer']['address'] ?? '',
            'codePostal'          => $data['employer']['postal_code'] ?? '',
            'ville'               => $data['employer']['city'] ?? '',
            'pays'                => $data['employer']['country'] ?? '',
            'nom_responsable'     => $data['employer']['security_officer_name'] ?? '',
            'nom_referent_sécurité'     => $data['employer']['security_officer_name'] ?? '',
            'fonction_referent_sécurité'=> $data['employer']['security_officer_position'] ?? '',
            'tel_resp'            => $data['employer']['security_officer_phone'] ?? '',
            'email_resp'          => $sdriToEmails ?? $data['employer']['security_officer_email'] ?? '',
            'nom_ref'             => $data['employer']['alternate_referent_name'] ?? '',
            'fonction_ref'        => $data['employer']['alternate_referent_position'] ?? '',
            'tel_ref'             => $data['employer']['alternate_referent_phone'] ?? '',
            'email_ref'           => $sdriCcEmails ?? $data['employer']['alternate_referent_email'] ?? '',
            'naf'                   => $data['employer']['naf'] ?? '',
            'tva_number'            => $data['employer']['vat_number'] ?? '',
            'codePostale'           => $data['employer']['postal_code'] ?? '',
            'telephone_refsec'      => $data['employer']['security_officer_phone'] ?? '',
            'email_refsec'          => $sdriToEmails ?? $data['employer']['security_officer_email'] ?? '',
            'nom_sdri'              => $data['admin']['signature_admin'] ?? '',

            // Employé
            'nom_employe'         => $data['employee']['last_name'] ?? '',
            'prenom_employe'      => $data['employee']['first_name'] ?? '',
            'nom_jeunefille_mère'     => $data['employee']['maiden_name'] ?? '',
            'date_naissance'      => $data['employee']['birthdate'] ?? '',
            'lieu_naissance'      => $data['employee']['birthplace'] ?? '',
            'codePostalNais'      => $data['employee']['birth_postal'] ?? '',
            'arrondissement'      => $data['employee']['birth_district'] ?? '',
            'nationalite'         => $data['employee']['nationality'] ?? '',
            'adresse_employe'     => $data['employee']['address'] ?? '',
            'ville_employe'       => $data['employee']['city'] ?? '',
            'pays_employe'        => $data['employee']['country'] ?? '',
            'codePostal_employe'  => $data['employee']['postal_code'] ?? '',
            'tel_employe'         => $data['employee']['phone'] ?? '',
            'email_employe'       => $data['employee']['email'] ?? '',
            'fonction_employe'    => $data['employee']['function'] ?? '',
            'date_embauche'       => $data['employee']['employment_date'] ?? '',
            'date_fin_contrat'    => $data['employee']['contract_end'] ?? '',
            'num_cni'             => $data['employee']['id_number'] ?? '',
            'nom_pere'            => $data['employee']['father_name'] ?? '',
            'prenom_pere'         => $data['employee']['father_first'] ?? '',
            'nom_mere'            => $data['employee']['mother_name'] ?? '',
            'prenom_mere'         => $data['employee']['mother_first'] ?? '',
            'nom'                   => $data['employee']['last_name'] ?? '',
            'prenom'                => $data['employee']['first_name'] ?? '',
            'journaissance'         => $data['employee']['birthdate'] ?? '',
            'employee_nationalite'  => $data['employee']['nationality'] ?? '',
            'employee_adresse'      => $data['employee']['address'] ?? '',
            'employee_codePostale'  => $data['employee']['postal_code'] ?? '',
            'employee_ville'        => $data['employee']['city'] ?? '',
            'employee_pays'         => $data['employee']['country'] ?? '',
            'securitesociale'       => $data['employee']['contract_type'] ?? '',
            'prenompere'            => $data['employee']['father_first'] ?? '',
            'prenommere'            => $data['employee']['mother_first'] ?? '',
            'telephone_employee'    => $data['employee']['phone'] ?? '',
            'email_employee'        => $data['employee']['email'] ?? '',
            'fonction_employee'     => $data['employee']['function'] ?? '',
            'numero_cni'            => $data['employee']['id_number'] ?? '',

            // Formations
            'fin_validation_formation_fluxel' => $data['training']['fluxel'] ?? '',
            'fin_validation_formation_gies1'  => $data['training']['gies'] ?? '',
            'fin_validation_formation_gies2'  => $data['training']['gies'] ?? '',
            'fin_validation_formation_atex0'   => $data['training']['atex'] ?? '',
            'fin_validation_formation_zar'    => $data['training']['zar'] ?? '',

            // Admin
            'observations'        => $data['admin']['observations'] ?? '',
            'decision'            => $data['admin']['access_decision'] ?? '',
            'date_expiration'     => $data['admin']['access_expiration_date'] ?? '',

            // --- Accès / Carte ---
            'motif_access'          => $data['access']['purpose'] ?? '',
            'type_edition'          => $data['access']['type'] ?? '',
            'type_emploi'           => $data['employee']['contract_type'] ?? '',
            'is_cdi'                => $data['employee']['contract_type'] === 'CDI' ? 'X' : '',
            'is_cdd'                => $data['employee']['contract_type'] === 'CDD' ? 'X' : '',
            'demand_date' => (new DateTime())->format('d/m/Y'),
        ];

        // Labels des documents
        $labels = [
            'id_card'          => 'Photocopie recto/verso CNI',
            'passport'         => 'Passeport',
            'residence_permit' => 'Carte de séjour',
            'photo'            => '1 photo d’identité',
            'bank_receipt'     => 'Justificatif bancaire du règlement',
            'proof_of_address_host'  => 'Justificatif de domicile ou certificat d’hébergement',
            'zar_decision'     => 'Copie décision préfectorale ZAR',
            'previous_card'    => 'Ancien titre de circulation',
            'loss_declaration' => 'Déclaration de perte/vol/casse',
            'taxi_card'        => 'Carte professionnelle de TAXI',
            'birth_certificate'=> 'Extrait acte de naissance avec filiation',
            'criminal_record_origin' => 'Casier judiciaire du pays d’origine',
            'criminal_record_nationality' => 'Casier judiciaire du pays de nationalité',
            'criminal_record_resident_country' => 'Casier judiciaire du pays de résidence',
            'refugee_attestation' => 'Attestation OFPRA',
            'refugee_criminal_record' => 'Casier judiciaire national (réfugiés)',
        ];

        // Génération dynamique de la liste
        $docsList = '';
        foreach ($labels as $key => $label) {
            if (!empty($data['docs'][$key])) {
                $docsList .= "☑ " . $label . "\n";
            }
        }

        // Injecter dans le mapping
        $mapping['documents_list'] = $docsList;

        // Construire la liste
        $accessList = '';
        foreach ($accessLabels as $key => $label) {
            $checked = in_array($key, $data['access']['locations'] ?? []) ? '☑' : '☐';
            $accessList .= $checked . ' ' . $label . "    Motif de l’accès : " . $data['access']['purpose'] . "\n";
        }

        // Injecter dans le mapping
        $mapping['access_souhaites'] = $accessList;


        // Remplacer les placeholders {{key}}
        foreach ($mapping as $key => $value) {
            $xml = str_replace('{{' . $key . '}}', htmlspecialchars((string)$value), $xml);
            $xml = str_replace('{' . $key . '}}', htmlspecialchars((string)$value), $xml);
        }

        file_put_contents($docXmlPath, $xml);

        $settingsPath = $tempDir . '/word/settings.xml';
        $settingsXml  = file_get_contents($settingsPath);

        // Générer un salt et un hash simple (SHA1)
        $password = preg_replace('/[^0-9]/', '', $mapping['date_naissance'] ?? '0000');
        $salt = random_bytes(16);
        $spinCount = 100000;
        $hash = base64_encode(sha1($password . $salt, true));
        $saltB64 = base64_encode($salt);

        // Construire le XML <w:documentProtection>
        $protection = sprintf(
            '<w:documentProtection w:edit="readOnly" w:enforcement="1" ' .
            'w:cryptProviderType="rsaFull" w:cryptAlgorithmClass="hash" ' .
            'w:cryptAlgorithmType="typeAny" w:cryptAlgorithmSid="4" ' .
            'w:cryptSpinCount="%d" w:hash="%s" w:salt="%s"/>',
            $spinCount,
            $hash,
            $saltB64
        );

        // Injecter juste avant </w:settings>
        $settingsXml = str_replace('</w:settings>', $protection . '</w:settings>', $settingsXml);

        file_put_contents($settingsPath, $settingsXml);


        // Rezipper
        $zip = new ZipArchive();
        $zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $files = new \RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tempDir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($tempDir) + 1);
            $zip->addFile($filePath, $relativePath);
        }
        $zip->close();

        return $this->file($outputPath, 'Demande_Fluxel_'.$step->getStepNumber().'.docx');
    }

    public function delete(Request $request, StepData $step, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $step->getStepId(), $request->request->get('_token'))) {
            $em->remove($step);
            $em->flush();
            $this->addFlash('success', 'L\'élément a été supprimé.');
        }

        return $this->redirectToRoute('person_access_list');
    }

    public function history(): Response
    {
        return $this->render('@MultiStepBundle/history.html.twig', []);
    }

    public function edit(StepData $step, Request $request): Response
    {
        // Réinitialiser le workflow et la session
        $this->workflowService->resetWorkflow();

        $stepDataArray = $this->workflowService->cleanMultistepData($step->getData());

        $currentStepId = null;

        foreach (array_reverse($stepDataArray) as $key => $subarray) {
            if (!empty($subarray)) {
                $currentStepId = $key;
                break;
            }
        }
        $this->workflowService->setCurrentStep($currentStepId);

        $this->workflowService->updateStepDatas($stepDataArray, 'session');

        return $this->handle($request, $step);
    }

    /**
     * Returns the referrer’s Symfony route name and parameters,
     * or null if it isn’t one of your routes.
     *
     * @return array{route: string, parameters: array}|null
     */
    protected function getReferrerRoute(Request $request): ?array
    {
        $referer = $request->headers->get('referer');
        if (!$referer) {
            return null;
        }

        // parse URL and keep only the path + query
        $uri = parse_url($referer, PHP_URL_PATH);
        $query = parse_url($referer, PHP_URL_QUERY);
        $pathInfo = $uri . ($query ? ('?' . $query) : '');

        try {
            // match() returns an array with _route, _controller, and any route params
            $matched = $this->urlMatcher->match($pathInfo);

            return [
                'route'      => $matched['_route'],
                'parameters' => array_filter(
                    $matched,
                    fn($key) => !str_starts_with((string)$key, '_'),
                    ARRAY_FILTER_USE_KEY
                ),
            ];
        } catch (ResourceNotFoundException $e) {
            return null;
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws DateMalformedStringException
     */
    public function validateInvestigation(Request $request, EntityManagerInterface $em): Response {

        // Retrieve and clean input values
        $cesarStepIds = $request->get('cesarStepIds', '');
        $stepDataList = explode('</p><p>', $cesarStepIds);

        // Extract all cesarStepIds and statuses
        $idsToProcess = [];
        foreach ($stepDataList as $line) {
            if (preg_match('/((FLUXEL-\d{4}-\d{4})\s+(\d))/', trim($line), $matches)) {
                $idsToProcess[] = [
                    'id' => $matches[2],
                    'value' => $matches[3],
                ];
            }
        }

        if (empty($idsToProcess)) {
            $this->addFlash('error', 'Veuillez renseigner les valeurs que la préfecture a envoyées par mail.');
            return $this->redirectToRoute('person_access_list');
        }

        // Fetch all the StepData entities for the cesarStepIds in one query
        $stepDataEntities = $this->multistepRepository
            ->findBy(['cesarStepId' => array_column($idsToProcess, 'id')]);

        $stepDataMap = [];
        foreach ($stepDataEntities as $stepData) {
            $stepDataMap[$stepData->getCesarStepId()] = $stepData;
        }

        // Iterate through each line and apply the transition
        foreach ($idsToProcess as $line) {
            $cesarStepId = $line['id'];
            $status = $line['value'];

            // Check if StepData entity exists for the given cesarStepId
            if (isset($stepDataMap[$cesarStepId])) {
                $stepData = $stepDataMap[$cesarStepId];

                // Define the transition based on the status
                $transition = '';
                switch ($status) {
                    case 0:
                        $transition = StepDataWorkflowService::TRANSITION_VALIDATE_INVESTIGATION;
                        break;
                    case 1:
                        $transition = StepDataWorkflowService::TRANSITION_INVESTIGATION_REJECTED;
                        break;
                    case 5:
                        $transition = StepDataWorkflowService::RESET_INVESTIGATION;
                        break;
                }

                $this->session->set('step_recheck_validate_investigation', true);
                $this->stepDataWorkflowService->applyTransition($stepData, $transition);
                $em->persist($stepData);
            }
        }
        $em->flush();
        return $this->redirectToRoute('person_access_list');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws DateMalformedStringException
     */
    public function rejectWithReason(Request $request): Response {

        // Retrieve and clean input values
        $rejectReason = $request->get('reject_reason', '');
        $rejectDemandId = $request->get('reject_demand_id', '');

        $stepData = $this->multistepRepository->findOneBy(['stepId' => $rejectDemandId]);
        $user = $stepData->getUser();

        // Send the templated email
        $this->notificationService->sendTemplatedEmail(
            from: $this->settingsService->get('system_email'),
            to: $user->getEmail(),
            subject: 'Erreurs détectées dans votre demande d’accès',
            cc: $this->getUser()?->getEmail(),
            template: 'emails/rejection_email.html.twig',
            templateVars: [
                'recipient_name' => $this->nameGuesser->guessName($user->getEmail()),
                'reject_reason' => $rejectReason,
            ]
        );

        $this->session->set('step_reject_reason', true);

        $this->stepDataWorkflowService->applyTransition($stepData, StepDataWorkflowService::TRANSITION_REJECT);

        return $this->redirectToRoute('person_access_list');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function applyTransition(StepData $step, string $transition): Response
    {
        try {
            $this->stepDataWorkflowService->applyTransition($step, $transition);
            $eventResult = $this->stepDataWorkflowService->getWorkflowMethodEvent()->getResult();
            if (is_array($eventResult)) {
                return match ($eventResult['type']) {
                    'twig_template' => $this->render($eventResult['template'], $eventResult['data']),
                    'json_response' => $this->json($eventResult['data']),
                    'redirect' => $this->redirect($eventResult['url']),
                    default => throw new UnexpectedValueException('Unhandled workflow event type: ' . $eventResult['type']),
                };
            }

            $this->addFlash('success', 'Transition appliquée avec succès.');
        } catch (Exception $e) {
            $this->addFlash('error', 'Erreur lors de l’application de la transition : ' . $e->getMessage());
        }

        return $this->redirectToRoute('person_access_list');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function submitReview(
        StepData $step,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $statuses = $request->request->all('field_status') ?? [];
        $comments = $request->request->all('field_comment') ?? [];

        $fieldReviews = [];
        $allIsValid = true;

        foreach ($statuses as $section => $fields) {
            foreach ($fields as $label => $status) {
                $comment = $comments[$section][$label] ?? null;
                if ($status !== 'valid' || $comment !== null) {
                    $fieldReviews[$section][$label] = [
                        'status' => $status,
                        'comment' => $comment,
                    ];
                }
                $allIsValid = $allIsValid && ($status === 'valid');
            }
        }
        if ($allIsValid) {
            $this->stepDataWorkflowService->approve($step);
        } else {
            $this->notifyUserDemandError($fieldReviews, $step);
            $this->session->set('step_recheck_review_information_submitted_' . $step->getStepId(), true);
            $this->stepDataWorkflowService->requestAdditionalInfo($step);
        }

        $step->setFieldReviews($fieldReviews);
        $em->persist($step);
        $em->flush();

        $this->addFlash('success', 'Revue enregistrée avec succès.');

        return $this->redirectToRoute('person_access_list');
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function notifyUserDemandError(array $fieldReviews, StepData $step): void
    {
        // Flatten invalid fields
        $flattenedErrors = [];

        foreach ($fieldReviews as $section => $fields) {
            foreach ($fields as $label => $info) {
                if ($info['status'] !== 'valid') {
                    $flattenedErrors[] = [
                        'step' => $section,
                        'field' => $label,
                        'comment' => $info['comment'] ?? '-',
                    ];
                }
            }
        }

        // Get user
        $user = $step->getUser();

        if (!$user || !filter_var($user->getEmail(), FILTER_VALIDATE_EMAIL)) {
            // Optionally log this
            return;
        }

        // Send the templated email
        $this->notificationService->sendTemplatedEmail(
            from: $this->settingsService->get('system_email'),
            to: $user->getEmail(),
            subject: 'Erreurs détectées dans votre demande d’accès',
            cc: $this->getUser()?->getEmail(),
            template: 'emails/validation_errors.html.twig',
            templateVars: [
                'errors' => $flattenedErrors,
            ]
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function runDeliverAndRespond(Request $request, StepData $step): Response
    {
        $this->stepDataWorkflowService->editCard($step);
        $result = $this->stepDataWorkflowService->getWorkflowMethodEvent()->getResult();
        return $this->buildResponseFromResult($request, $result);
    }

    private function buildResponseFromResult(Request $request, $result): Response
    {// AJAX
        if ($request->isXmlHttpRequest()) {
            if (is_array($result)) {
                if (($result['type'] ?? null) === 'json_response') {
                    return $this->json($result['data']);
                }
                if (($result['type'] ?? null) === 'redirect' && !empty($result['url'])) {
                    return $this->json(['status' => 'ok', 'redirect' => $result['url']]);
                }
            }
            return $this->json(['status' => 'ok', 'reload' => true]);
        }

        // Non-AJAX
        if (is_array($result)) {
            if (($result['type'] ?? null) === 'redirect' && !empty($result['url'])) {
                return $this->redirect($result['url']);
            }
            if (($result['type'] ?? null) === 'twig_template') {
                return $this->render($result['template'], $result['data']);
            }
            if (($result['type'] ?? null) === 'json_response') {
                return $this->json($result['data']);
            }
        }

        return $this->redirectToRoute('person_access_list');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function cardPlace(Request $request, StepData $step, EntityManagerInterface $em): Response
    {
        $available = $step->getInternalData('person_step_one', 'access_locations');
        $available = is_array($available) ? $available : [];

        // Auto-sélection si un seul site
        if (!$step->getInternalData('person_step_six', 'card_place') && count($available) === 1) {
            $step->setInternalData('person_step_six', 'card_place', $available[0]);
            $em->flush();
            return $this->runDeliverAndRespond($request, $step);
        }

        $initial = ['card_place' => $step->getInternalData('person_step_six', 'card_place') ?: null];

        // Libellés lisibles + fallback si $available vide
        $values   = $available ?: ['fos','lavera'];
        $labels   = array_map(
            static fn (string $c) => $c === 'fos' ? 'Port pétrolier de Fos' : 'Port pétrolier de Lavéra',
            $values
        );
        $choices = array_combine($labels, $values);

        $form = $this->createFormBuilder($initial)
            ->add('card_place', ChoiceType::class, [
                'label'       => 'Lieu de retrait du titre',
                'placeholder' => count($choices) > 1 ? '— Sélectionner —' : false,
                'choices'     => $choices,
                'required'    => true,
                'expanded'    => true,
                'multiple'    => false,
                'data'        => count($choices) === 1 ? array_values($choices)[0] : $initial['card_place'],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $choice = $form->get('card_place')->getData();
            $step->setInternalData('person_step_six', 'card_place', $choice);
            $em->flush();
            return $this->runDeliverAndRespond($request, $step);
        }

        // Fragment (AJAX) ou page
        if ($request->isXmlHttpRequest()) {
            return $this->render('@MultiStepBundle/modal/_card_place_form.html.twig', [
                'form' => $form->createView(),
                'step' => $step,
            ]);
        }

        return $this->render('@MultiStepBundle/card_place.html.twig', [
            'form' => $form->createView(),
            'step' => $step,
            'locations' => $available,
        ]);
    }

}