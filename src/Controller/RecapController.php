<?php

namespace App\Controller;

use App\Service\EntityManagerServices\RecapManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recap', name: 'app_recap_')]
class RecapController extends AbstractController
{
    private readonly RecapManagerService $recapManager;

    public function __construct(RecapManagerService $recapManager)
    {
        $this->recapManager = $recapManager;
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $recapData = $this->recapManager->prepareRecapData($user);

        return $this->render('recap/index.html.twig', $recapData);
    }

    #[Route('/update', name: 'update', methods: ['POST','GET'])]
    public function recapUpdate(): Response
    {
        $user = $this->getUser();
        $this->recapManager->updateRecapStatus($user);

        $this->addFlash('demandeUpdate', 'Votre demande de titre de circulation a bien été mise à jour.');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/show/{id}', name: 'show', methods: ['GET'])]
    public function recapShow(Request $request, int $id): Response
    {
        $lastRoute = $this->recapManager->getLastRoute($request);

        $recapData = $this->recapManager->prepareRecapDataById($id);
        $recapData['lastRoute'] = $lastRoute;

        return $this->render('recap/index.html.twig', $recapData);
    }
}
