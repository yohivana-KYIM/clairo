<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MesDemandesController extends AbstractController
{
    #[Route('/mesdemandes', name: 'app_mes_demandes')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $problemeDeCarte = $user->getProblemecarte();
        $demandeTitreCirculation = $user->getDemandes();
        $getLastRoute = $request->headers->get('referer');
        $lastRoute = basename((string) $getLastRoute);

        return $this->render('mes_demandes/index.html.twig', [
            'controller_name' => 'MesDemandesController',
            'demandes' => $demandeTitreCirculation,
            'problemeCarte' => $problemeDeCarte,
            'lastRoute' => $lastRoute,
        ]);
    }

    #[Route('/{id}/mesdemandes', name: 'app_mes_demandes_show')]
    public function maDemande(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $problemeDeCarte = $user->getProblemecarte();
        $demandeTitreCirculation = $user->getDemandes();

        return $this->render('mes_demandes/index.html.twig', [
            'controller_name' => 'MesDemandesController',
            'demandes' => $demandeTitreCirculation,
            'problemeCarte' => $problemeDeCarte,
        ]);
    }
}

