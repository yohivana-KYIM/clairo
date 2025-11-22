<?php

namespace App\Controller\Vehicules;

use App\Entity\DemandeTitreVehicule;
use App\Entity\User;
use App\Form\Vehicules\InfoComplementaireType;
use App\Entity\InfoComplementaireVehicule as InfoComplementaire;
use App\Service\EntityManagerServices\Vehicules\InfoComplementaireManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('vehicule/info/complementaire', name: 'app_vehicule_info_complementaire_')]
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

        return $this->render('vehicule_info_complementaire/index.html.twig', [
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
            'vehicule_info_complementaire/new.html.twig',
            $demandeTitreCirculation,
            'app_vehicule_document_personnel_new'
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(InfoComplementaire $infoComplementaire): Response
    {
        return $this->render('vehicule_info_complementaire/show.html.twig', [
            'vehicule_info_complementaire' => $infoComplementaire,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, InfoComplementaire $infoComplementaire): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $this->infoManager->getUserLatestRequest($user);
        $nextRoute = $demandeTitreCirculation->getDocpersonnel()
            ? ['route' => 'app_vehicule_document_personnel_edit', 'params' => ['id' => $demandeTitreCirculation->getDocPersonnel()->getId()]]
            : ['route' => 'app_vehicule_document_personnel_new', 'params' => []];

        return $this->handleForm(
            $request,
            $infoComplementaire,
            'vehicule_info_complementaire/edit.html.twig',
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

        return $this->redirectToRoute('app_vehicule_info_complementaire_index');
    }

    private function handleForm(
        Request $request,
        InfoComplementaire $infoComplementaire,
        string $template,
        DemandeTitreVehicule $demandeTitreCirculation,
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
