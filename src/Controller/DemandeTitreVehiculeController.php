<?php

namespace App\Controller;

use App\Entity\DemandeTitreVehicule;
use App\Entity\User;
use App\Repository\DemandeTitreVehiculeRepository;
use App\Service\EntityManagerServices\DemandeTitreVehiculeManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/demandetitrevehicule')]
class DemandeTitreVehiculeController extends AbstractController
{
    public function __construct(
        private readonly DemandeTitreVehiculeManagerService $demandeManagerService,
        private readonly DemandeTitreVehiculeRepository     $demandeRepository
    ) {}

    #[Route('/', name: 'app_demande_titre_vehicule_index', methods: ['GET'])]
    public function index(): Response
    {
        $demands = $this->demandeRepository->findAll();
        return $this->render('demande_titre_vehicule/index.html.twig', [
            'demande_titre_vehicules' => $demands,
        ]);
    }

    #[Route('/new', name: 'app_demande_titre_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $clientIp = $request->getClientIp();

        $demand = $this->demandeManagerService->findOrCreateDemand($clientIp);

        if (empty($demand)) {
            return $this->redirectToRoute('app_vehicule_intervention_new');
        }

        if (empty($demand->getIntervention())) {
            return $this->redirectToRoute('app_vehicule_intervention_new');
        }

        if ($demand->getValidatedAt() !== null) {
            return $this->redirectToRoute('app_recap_index');
        }
        return $this->redirectToRoute('app_demande_titre_vehicule_edit', ['id' => $demand->getId()]);
    }

    #[Route('/recap', name: 'app_demande_titre_vehicule_recap')]
    public function recap(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $demand = $user->getDemandeVehicules()->last();

        if (!$demand) {
            throw $this->createNotFoundException('No demand found for recap.');
        }

        $this->demandeManagerService->validateDemand($demand);
        $this->addFlash('demandeValide', 'Votre demande de titre de vehicule a bien été pris en compte.');

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_demande_titre_vehicule_show', methods: ['GET'])]
    public function show(DemandeTitreVehicule $demandeTitreVehicule): Response
    {
        return $this->render('demande_titre_vehicule/show.html.twig', [
            'demande_titre_vehicule' => $demandeTitreVehicule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_titre_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(DemandeTitreVehicule $demandeTitreVehicule): Response
    {
        $redirectConditions = $this->demandeManagerService->getRedirectConditions($demandeTitreVehicule);

        foreach ($redirectConditions as $redirect) {
            if ($redirect['condition']) {
                return $this->redirectToRoute($redirect['route'], $redirect['params'], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->redirectToRoute('app_recap_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_demande_titre_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, DemandeTitreVehicule $demandeTitreVehicule): Response
    {
        if ($this->isCsrfTokenValid('delete' . $demandeTitreVehicule->getId(), $request->request->get('_token'))) {
            $this->demandeManagerService->getEntityManager()->remove($demandeTitreVehicule);
            $this->demandeManagerService->getEntityManager()->flush();
        }

        $lastRoute = basename((string) $request->headers->get('referer'));

        if ($lastRoute === 'mesdemandes') {
            return $this->redirectToRoute('app_mes_demandes');
        }

        if ($lastRoute === 'demandesdri') {
            return $this->redirectToRoute('app_demande_sdri');
        }

        return $this->redirectToRoute('app_mes_demandes');
    }
}
