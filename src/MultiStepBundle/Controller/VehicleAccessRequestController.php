<?php

namespace App\MultiStepBundle\Controller;

use App\Entity\User;
use App\MultiStepBundle\Application\VehicleAccessWorkflowService;
use App\MultiStepBundle\Domain\Vehicule\AbstractVehicleStep;
use App\MultiStepBundle\Entity\StepData;
use App\MultiStepBundle\Persistence\Repository\MultistepRepository;
use App\Service\Workflow\Classes\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class VehicleAccessRequestController extends AbstractController
{

    public function __construct(
        private readonly VehicleAccessWorkflowService $workflowService,
        private readonly UrlMatcherInterface $urlMatcher,
        private readonly MultistepRepository $multistepRepository,
        private readonly RouterInterface $router,
        private readonly NotificationService $notificationService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function handle(Request $request): Response
    {
        $existingNames = $this->multistepRepository->findAllStepNamesForUser($this->getUser());
        $currentStep = $this->workflowService->getCurrentStep();
        $data        = $currentStep->processLoadedData(
            $this->workflowService->loadData()
        );

        $errors = [];
        if (method_exists($currentStep, 'checkStepDatas')) {
            $requestDatas = array_values($request->files->all());
            if ($requestDatas)
                $errors = $currentStep->checkStepDatas(reset($requestDatas));
        }

        if ($currentStep instanceof AbstractVehicleStep) {
            $currentStep->setPreviousFormData($data);
        }

        $form = $this->createForm(
            $currentStep->getFormType(),
            $data
        );
        $form->handleRequest($request);

        if (empty($errors)) {
            if ($form->isSubmitted() && $form->isValid()) {
                $currentStep->process($form);
                $this->workflowService->saveData(
                    $currentStep->getData()
                );

                if ($this->workflowService->isComplete()) {
                    return $this->redirectToRoute('vehicle_access_review');
                }

                $this->workflowService->advance();

                return $this->redirectToRoute('vehicle_access_request');
            }
        } else {
            foreach ($errors as $key => $errorgroup) {
                foreach ($errorgroup as $error) {
                    $this->addFlash('error', sprintf('%s: %s', $this->translator->trans($key), $error));
                }
            }
        }

        return $this->render('@MultiStepBundle/vehicle/step.html.twig', [
            'form'          => $form->createView(),
            'current_step'  => $currentStep->getName(),
            'step_trail'    => $this->workflowService->generateStepTrail(),
            'buttons'       => $this->workflowService->generateButtons(),
            'step_entity'   => 'Les véhicules',
            'step_asset'    => $currentStep->getCustomScriptUrl(),
            'data'          => $data,
            'step_id'             => 0,
            'step_number'             => $currentStep->getId(),
            'existingNames'      => $existingNames,
            'errors' => $errors,
        ]);
    }

    public function goBack(): Response
    {
        $this->workflowService->goBack();
        return $this->redirectToRoute('vehicle_access_request');
    }

    public function list(): Response {
        $user = $this->getUser();
        assert($user instanceof User);
        $vehicleAccess = $this->multistepRepository->findAccessStepsForUser($user, 'vehicle');

        return $this->render('@MultiStepBundle/vehicle/list.html.twig', [
            'steps' => $vehicleAccess,
        ]);
    }

    public function show(StepData $step, EntityManagerInterface $entityManager): Response
    {
        $allData = $step->getData();
        $existingNames = $this->multistepRepository->findAllStepNamesForUser($this->getUser());
        $routeParams = [];
        $routeParams['id'] = $step->getStepId();

        return $this->render('@MultiStepBundle/vehicle/review.html.twig', [
            'all_data'            => $allData,
            'review_back_link'    => 'vehicle_access_list',
            'review_persist_link' => $this->router->generate('vehicle_access_persist', $routeParams),
            'review_edit_link'    => 'vehicle_access_edit',
            'step_id'             => $step->getStepId(),
            'step_number'             => $step->getStepNumber(),
            'existingNames'      => $existingNames
        ]);
    }

    public function delete(Request $request, StepData $step, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $step->getStepId(), $request->request->get('_token'))) {
            $em->remove($step);
            $em->flush();
            $this->addFlash('success', 'L\'élément a été supprimé.');
        }
        return $this->redirectToRoute('vehicle_access_list');
    }

    public function edit(StepData $step): Response
    {
        $this->workflowService->resetWorkflow();

        $stepDataArray = $this->workflowService->cleanMultistepData(
            $step->getData()
        );

        // find last non-empty index
        $current = null;
        foreach (array_reverse($stepDataArray, true) as $key => $sub) {
            if (!empty($sub)) {
                $current = $key;
                break;
            }
        }

        if ($current) {
            $this->workflowService->setCurrentStep($current);
            $this->workflowService->updateStepDatas(
                $stepDataArray,
                'session'
            );
        }

        return $this->redirectToRoute('vehicle_access_request');
    }

    public function history(): Response
    {
        return $this->render('@MultiStepBundle/history.html.twig', []);
    }

    public function review( ?StepData $stepData = null): Response
    {
        $existingStepNames = $this->multistepRepository->findAllStepNamesForUser($this->getUser());
        $allData     = $this->workflowService->getAllData(true);

        $routeParams = [];
        if ($stepData !== null) {
            $routeParams['id'] = $stepData->getStepId();
        }

        return $this->render('@MultiStepBundle/vehicle/review.html.twig', [
            'all_data'            => $allData,
            'review_back_link'    => 'vehicle_access_request',
            'review_persist_link' => $this->router->generate('vehicle_access_persist', $routeParams),
            'review_edit_link'    => 'vehicle_access_edit',
            'existingNames' => $existingStepNames,
            'step_number'             => '',
        ]);
    }

    public function persist(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        try {
            $currentStep = $this->workflowService->getCurrentStep();

            // save only posted step
            $posted = $request->request->all();
            $filesData = $request->files->all();

            foreach ($filesData as $fileKey => $fileArray) {
                foreach ($fileArray as $key => $value) {
                    if ($value instanceof UploadedFile) {
                        $currentStep->handleFileUpload($key, $value, $posted[$fileKey]);
                    }
                }
            }
            if (array_key_exists('_token', $posted)) {
                unset($posted['_token']);
            }
            $stepData = array_shift($posted);
            if (!is_array($stepData)) {
                $stepData = [];
            }


            $this->workflowService->saveData(
                $stepData ?? [],
                'single_table'
            );

            $this->addFlash('success', 'Les données ont été enregistrées.');
            return $this->redirectToRoute('vehicle_access_list');
        } catch (\Exception $e) {
            $this->addFlash('error', 'une erreur est survenue; consultez votre messagerie et contactez l\'administrateur.');
            $this->notificationService->sendAppNotification($user, 'Une erreur est survenue:' . $e->getMessage());
            return $this->redirectToRoute('vehicle_access_request');
        }
    }

    protected function getReferrerRoute(Request $request): ?array
    {
        $referer = $request->headers->get('referer');
        if (!$referer) {
            return null;
        }

        $uri   = parse_url($referer, PHP_URL_PATH);
        $query = parse_url($referer, PHP_URL_QUERY);
        $path  = $uri . ($query ? ('?' . $query) : '');

        try {
            $matched = $this->urlMatcher->match($path);
            return [
                'route'      => $matched['_route'],
                'parameters' => array_filter(
                    $matched,
                    fn($k) => 0 !== strpos((string) $k, '_'),
                    ARRAY_FILTER_USE_KEY
                ),
            ];
        } catch (ResourceNotFoundException $e) {
            return null;
        }
    }
}
