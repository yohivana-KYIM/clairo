<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class WorkflowController extends AbstractController
{
    #[Route('/workflow/validate-investigation', name: 'workflow_validate_investigation', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SDRI')]
    public function validateInvestigation(): Response {
        return $this->render('@MultiStepBundle/transition/before_validate_investigation.html.twig', [
            'cesarStepIds' => '',
        ]);
    }
}
