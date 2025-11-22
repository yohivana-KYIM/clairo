<?php

namespace App\Controller;

use App\MultiStepBundle\Persistence\Repository\MultistepRepository;
use App\Service\EntityManagerServices\GardienManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gardien', name: 'app_gardien_')]
class GardienController extends AbstractController
{
    private readonly GardienManagerService $gardienManager;

    public function __construct(GardienManagerService $gardienManager)
    {
        $this->gardienManager = $gardienManager;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('gardien/index.html.twig', [
            'controller_name' => 'GardienController',
        ]);
    }

    #[Route('/tableau', name: 'tableau')]
    public function tableau(MultistepRepository $multistepRepository): Response
    {
        $data = $this->gardienManager->getTableauData($multistepRepository);

        return $this->render('gardien/tableau.html.twig', $data);
    }

    #[Route('/tableau/commentaire/{id}/{parametre}', name: 'commentaire')]
    public function commentaire(Request $request, int $id, string $parametre): Response
    {
        $comment = $request->request->get('commentaireDeliveryCarte');

        $redirectRoute = $this->gardienManager->handleCommentaire(
            $id,
            $parametre,
            $comment,
            $this->getUser()
        );

        return $this->redirectToRoute($redirectRoute, [], Response::HTTP_SEE_OTHER);
    }
}
