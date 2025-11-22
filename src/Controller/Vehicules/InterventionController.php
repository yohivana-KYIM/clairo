<?php

namespace App\Controller\Vehicules;

use App\Entity\DemandeTitreVehicule;
use App\Entity\Intervention;
use App\Entity\User;
use App\Service\EntityManagerServices\Vehicules\InterventionManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vehicule_intervention', name: 'app_vehicule_intervention_')]
class InterventionController extends AbstractController
{
    private readonly InterventionManagerService $vehiculeInterventionManager;

    public function __construct(InterventionManagerService $vehiculeInterventionManager)
    {
        $this->vehiculeInterventionManager = $vehiculeInterventionManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $interventions = $this->vehiculeInterventionManager->getAll();

        return $this->render('vehicule_intervention/index.html.twig', [
            'interventions' => $interventions,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $intervention = new Intervention();
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $this->vehiculeInterventionManager->getUserLatestRequest($user);

        return $this->handleForm(
            $request,
            $intervention,
            'vehicule_intervention/new.html.twig',
            $demandeTitreCirculation,
            'app_vehicule_adresse_new'
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Intervention $intervention): Response
    {
        return $this->render('vehicule_intervention/show.html.twig', [
            'intervention' => $intervention,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Intervention $intervention): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $this->vehiculeInterventionManager->getUserLatestRequest($user);
        $nextRoute = $this->vehiculeInterventionManager->determineNextRoute($demandeTitreCirculation);

        return $this->handleForm(
            $request,
            $intervention,
            'vehicule_intervention/edit.html.twig',
            $demandeTitreCirculation,
            $nextRoute['route'],
            $nextRoute['params']
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Intervention $intervention): Response
    {
        if ($this->isCsrfTokenValid('delete' . $intervention->getId(), $request->request->get('_token'))) {
            $this->vehiculeInterventionManager->delete($intervention);
        }

        return $this->redirectToRoute('app_intervention_index');
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/sendEmailIntervention/{id}', name: 'sendEmail', methods: ['POST'])]
    public function sendEmailIntervention(Request $request): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $this->vehiculeInterventionManager->sendEmailIntervention($request, $user);

        $this->addFlash('success', 'Votre demande d\'ajout d\'entreprise a bien été prise en compte.');
        return $this->redirectToRoute('app_home');
    }

    private function handleForm(
        Request $request,
        Intervention $intervention,
        string $template,
        DemandeTitreVehicule $demandeTitreCirculation,
        string $redirectRoute,
        array $redirectParams = []
    ): Response {
        $form = $this->vehiculeInterventionManager->createForm($intervention, $demandeTitreCirculation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entreprise = $form->get('entreprise')->getData();
            $demandeTitreCirculation->setEntreprise($entreprise);
            $this->vehiculeInterventionManager->saveIntervention($intervention, $demandeTitreCirculation);

            return $this->redirectToRoute($redirectRoute, $redirectParams);
        }

        return $this->render($template, $this->vehiculeInterventionManager->prepareFormData($intervention, $demandeTitreCirculation, $form));
    }
}
