<?php

namespace App\Controller\sdri;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SdriController extends AbstractController
{
    #[Route('/sdri', name: 'app_sdri')]
    public function index(): Response
    {
        return $this->render('sdri/home_sdri.html.twig', [
            'controller_name' => 'SdriController',
        ]);
    }
}
