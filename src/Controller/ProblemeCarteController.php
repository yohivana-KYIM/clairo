<?php

namespace App\Controller;

use App\Entity\ProblemeCarte;
use App\Entity\User;
use App\Form\ProblemeCarteType;
use App\Service\EntityManagerServices\ProblemeCarteManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/problemecarte', name: 'app_probleme_carte_')]
class ProblemeCarteController extends AbstractController
{
    private readonly ProblemeCarteManagerService $problemeCarteManager;

    public function __construct(ProblemeCarteManagerService $problemeCarteManager)
    {
        $this->problemeCarteManager = $problemeCarteManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $problemeCartes = $this->problemeCarteManager->getAll();

        return $this->render('probleme_carte/index.html.twig', [
            'probleme_cartes' => $problemeCartes,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $problemeCarte = new ProblemeCarte();
        $user = $this->getUser();
        assert($user instanceof User);

        $form = $this->createForm(ProblemeCarteType::class, $problemeCarte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->problemeCarteManager->saveNewProblemeCarte($problemeCarte, $user);

            return $this->redirectToRoute('app_home');
        }

        return $this->render('probleme_carte/new.html.twig', [
            'probleme_carte' => $problemeCarte,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(ProblemeCarte $problemeCarte, Request $request): Response
    {
        $lastRoute = $this->problemeCarteManager->getLastRoute($request);

        return $this->render('probleme_carte/show.html.twig', [
            'probleme_carte' => $problemeCarte,
            'lastRoute' => $lastRoute,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProblemeCarte $problemeCarte): Response
    {
        $form = $this->createForm(ProblemeCarteType::class, $problemeCarte);
        $form->handleRequest($request);
        $user = $this->getUser();
        assert($user instanceof User);

        $lastRoute = $this->problemeCarteManager->getLastRoute($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->problemeCarteManager->updateProblemeCarte($problemeCarte);

            return $this->redirectToRoute('app_mes_demandes');
        }

        return $this->render('probleme_carte/edit.html.twig', [
            'probleme_carte' => $problemeCarte,
            'form' => $form,
            'lastRoute' => $lastRoute,
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, ProblemeCarte $problemeCarte): Response
    {
        $lastRoute = $this->problemeCarteManager->getLastRoute($request);

        if ($this->isCsrfTokenValid('delete' . $problemeCarte->getId(), $request->request->get('_token'))) {
            $this->problemeCarteManager->deleteProblemeCarte($problemeCarte);
        }

        return $this->redirectToRoute($lastRoute === 'mesdemandes' ? 'app_mes_demandes' : 'app_demande_sdri');
    }
}
