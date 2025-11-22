<?php

namespace App\MultiStepBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\MultiStepBundle\Application\MultiStepBundleWorkflowService;

class MultiStepBundleController extends AbstractController
{
    private MultiStepBundleWorkflowService $workflowService;

    public function __construct(MultiStepBundleWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function handle(Request $request): Response
    {
        $currentStep = $this->workflowService->getCurrentStep();
        $form = $this->createForm($currentStep->getFormType());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentStep->process($form);
            $this->workflowService->advance();

            if ($this->workflowService->isComplete()) {
                return $this->redirectToRoute('vehicle_access_review');
            }

            return $this->redirectToRoute('workflow_handle');
        }

        return $this->render('@MultiStepBundle/step.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function review(Request $request): Response
    {
        $allData = $this->workflowService->getAllData();

        return $this->render('@MultiStepBundle/review.html.twig', [
            'all_data' => $allData,
        ]);
    }
}
