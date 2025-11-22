<?php

namespace App\Controller;

use App\Entity\EtatCivil;
use App\Entity\User;
use App\Form\EtatCivilType;
use App\Repository\EtatCivilRepository;
use App\Service\EntityManagerServices\EtatCivilManagerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/etat/civil')]
class EtatCivilController extends AbstractController
{
    public const ROUTE_INDEX = 'app_etat_civil_index';
    public const ROUTE_FILIATION_NEW = 'app_filiation_new';
    public const ROUTE_FILIATION_EDIT = 'app_filiation_edit';

    #[Route('/', name: 'app_etat_civil_index', methods: ['GET'])]
    public function index(EtatCivilRepository $etatCivilRepository): Response
    {
        return $this->render('etat_civil/index.html.twig', [
            'etat_civils' => $etatCivilRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_etat_civil_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EtatCivilManagerService $etatCivilManager): Response
    {
        $etatCivil = new EtatCivil();
        $form = $this->createForm(EtatCivilType::class, $etatCivil);

        /** @var User $user */
        $user = $this->getUser();
        $context = $etatCivilManager->initializeEtatCivilForUser($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etatCivilManager->saveEtatCivil($etatCivil, $context['demande']);

            return $this->redirectToRoute(self::ROUTE_FILIATION_NEW, [], Response::HTTP_SEE_OTHER);
        }

        $viewContext = array_merge($context, ['form' => $form]);
        return $this->render('etat_civil/new.html.twig', $viewContext);
    }

    #[Route('/{id}', name: 'app_etat_civil_show', methods: ['GET'])]
    public function show(EtatCivil $etatCivil): Response
    {
        return $this->render('etat_civil/show.html.twig', [
            'etat_civil' => $etatCivil,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etat_civil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EtatCivil $etatCivil, EtatCivilManagerService $etatCivilManager): Response
    {
        $form = $this->createForm(EtatCivilType::class, $etatCivil);
        $user = $this->getUser();
        assert($user instanceof User);
        $context = $etatCivilManager->initializeEtatCivilForUser($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etatCivilManager->getEntityManager()->flush();

            $filiation = $context['filiation'];
            $routeName = $filiation ? self::ROUTE_FILIATION_EDIT : self::ROUTE_FILIATION_NEW;
            $routeParams = $filiation ? ['id' => $filiation->getId()] : [];

            return $this->redirectToRoute($routeName, $routeParams, Response::HTTP_SEE_OTHER);
        }

        $viewContext = array_merge($context, ['form' => $form]);
        return $this->render('etat_civil/edit.html.twig', $viewContext);
    }

    #[Route('/{id}', name: 'app_etat_civil_delete', methods: ['POST'])]
    public function delete(Request $request, EtatCivil $etatCivil, EtatCivilManagerService $etatCivilManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $etatCivil->getId(), $request->request->get('_token'))) {
            $etatCivilManager->deleteEtatCivil($etatCivil);
        }

        return $this->redirectToRoute(self::ROUTE_INDEX, [], Response::HTTP_SEE_OTHER);
    }
}
