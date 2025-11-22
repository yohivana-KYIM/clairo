<?php

namespace App\Controller;

use App\Entity\InfoComplementaire;
use App\Entity\User;
use App\Form\InfoComplementaireType;
use App\Service\EntityManagerServices\InfoComplementaireManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/info/complementaire', name: 'app_info_complementaire_')]
class InfoComplementaireController extends AbstractController
{
    private readonly InfoComplementaireManagerService $infoManager;

    public function __construct(InfoComplementaireManagerService $infoManager)
    {
        $this->infoManager = $infoManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $infoComplementaires = $this->infoManager->getAll();

        return $this->render('info_complementaire/index.html.twig', [
            'info_complementaires' => $infoComplementaires,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $infoComplementaire = new InfoComplementaire();
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $this->infoManager->getUserLatestRequest($user);

        return $this->handleForm(
            $request,
            $infoComplementaire,
            'info_complementaire/new.html.twig',
            $demandeTitreCirculation,
            'app_document_personnel_new'
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(InfoComplementaire $infoComplementaire): Response
    {
        return $this->render('info_complementaire/show.html.twig', [
            'info_complementaire' => $infoComplementaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InfoComplementaire $infoComplementaire): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $this->infoManager->getUserLatestRequest($user);
        $nextRoute = $demandeTitreCirculation->getDocpersonnel()
            ? ['route' => 'app_document_personnel_edit', 'params' => ['id' => $demandeTitreCirculation->getDocPersonnel()->getId()]]
            : ['route' => 'app_document_personnel_new', 'params' => []];

        return $this->handleForm(
            $request,
            $infoComplementaire,
            'info_complementaire/edit.html.twig',
            $demandeTitreCirculation,
            $nextRoute['route'],
            $nextRoute['params']
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, InfoComplementaire $infoComplementaire): Response
    {
        if ($this->isCsrfTokenValid('delete' . $infoComplementaire->getId(), $request->request->get('_token'))) {
            $this->infoManager->delete($infoComplementaire);
        }

        return $this->redirectToRoute('app_info_complementaire_index');
    }

    private function handleForm(
        Request $request,
        InfoComplementaire $infoComplementaire,
        string $template,
        $demandeTitreCirculation,
        string $redirectRoute,
        array $redirectParams = []
    ): Response {
        $form = $this->createForm(InfoComplementaireType::class, $infoComplementaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->infoManager->saveInfoComplementaire($infoComplementaire, $demandeTitreCirculation);
            return $this->redirectToRoute($redirectRoute, $redirectParams);
        }

        return $this->render($template, array_merge(
            ['form' => $form->createView(), 'infoComplementaire' => $infoComplementaire],
            $this->infoManager->prepareDemandeDetails($demandeTitreCirculation)
        ));
    }
}
