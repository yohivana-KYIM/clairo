<?php

namespace App\MultiStepBundle\Controller;

use App\MultiStepBundle\Application\VehicleAccessWorkflowService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class APIWorkflowController extends AbstractController
{
    private VehicleAccessWorkflowService $workflowService;

    public function __construct(VehicleAccessWorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    public function getCurrentStep(): Response
    {
        $currentStep = $this->workflowService->getCurrentStep();
        return $this->json([
            'step_id' => $currentStep->getId(),
            'form_type' => $currentStep->getFormType(),
        ]);
    }

    public function advanceStep(): Response
    {
        $this->workflowService->advance();
        return $this->json(['status' => 'success']);
    }
}