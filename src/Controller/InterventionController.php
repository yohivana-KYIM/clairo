<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\User;
use App\Form\InterventionType;
use App\Service\EntityManagerServices\InterventionManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/intervention', name: 'app_intervention_')]
class InterventionController extends AbstractController
{
    private readonly InterventionManagerService $interventionManager;

    public function __construct(InterventionManagerService $interventionManager)
    {
        $this->interventionManager = $interventionManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $interventions = $this->interventionManager->getAll();

        return $this->render('intervention/index.html.twig', [
            'interventions' => $interventions,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $intervention = new Intervention();
        $user = $this->getUser();
        $demandeTitreCirculation = $this->interventionManager->getUserLatestRequest($user);

        return $this->handleForm(
            $request,
            $intervention,
            'intervention/new.html.twig',
            $demandeTitreCirculation,
            'app_etat_civil_new'
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Intervention $intervention): Response
    {
        return $this->render('intervention/show.html.twig', [
            'intervention' => $intervention,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Intervention $intervention): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $this->interventionManager->getUserLatestRequest($user);
        $nextRoute = $this->interventionManager->determineNextRoute($demandeTitreCirculation);

        return $this->handleForm(
            $request,
            $intervention,
            'intervention/edit.html.twig',
            $demandeTitreCirculation,
            $nextRoute['route'],
            $nextRoute['params']
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Intervention $intervention): Response
    {
        if ($this->isCsrfTokenValid('delete' . $intervention->getId(), $request->request->get('_token'))) {
            $this->interventionManager->delete($intervention);
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
        $this->interventionManager->sendEmailIntervention($request, $user);

        $this->addFlash('success', 'Votre demande d\'ajout d\'entreprise a bien été prise en compte.');
        return $this->redirectToRoute('app_home');
    }

    private function handleForm(
        Request $request,
        Intervention $intervention,
        string $template,
        $demandeTitreCirculation,
        string $redirectRoute,
        array $redirectParams = []
    ): Response {
        $form = $this->interventionManager->createForm($intervention, $demandeTitreCirculation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entreprise = $form->get('entreprise')->getData();
            $demandeTitreCirculation->setEntreprise($entreprise);
            $this->interventionManager->saveIntervention($intervention, $demandeTitreCirculation);

            return $this->redirectToRoute($redirectRoute, $redirectParams);
        }

        return $this->render($template, $this->interventionManager->prepareFormData($intervention, $demandeTitreCirculation, $form));
    }
}
