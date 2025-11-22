<?php

namespace App\MultiStepBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ErrorStepController extends AbstractController
{
    public function handleError(string $errorMessage): Response
    {
        return $this->render('@MultiStepBundle/error.html.twig', [
            'error_message' => $errorMessage,
        ]);
    }
}