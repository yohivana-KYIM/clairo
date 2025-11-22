<?php

namespace App\Controller;

use App\Entity\DemandeTitreCirculation;
use App\Entity\User;
use App\Repository\DemandeTitreCirculationRepository;
use App\Service\EntityManagerServices\DemandeTitreCirculationManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/demandetitrecirculation')]
class DemandeTitreCirculationController extends AbstractController
{
    public function __construct(
        private readonly DemandeTitreCirculationManagerService $demandeManagerService,
        private readonly DemandeTitreCirculationRepository     $demandeRepository
    ) {}

    #[Route('/', name: 'app_demande_titre_circulation_index', methods: ['GET'])]
    public function index(): Response
    {
        $demands = $this->demandeRepository->findAll();
        return $this->render('demande_titre_circulation/index.html.twig', [
            'demande_titre_circulations' => $demands,
        ]);
    }

    #[Route('/new', name: 'app_demande_titre_circulation_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $clientIp = $request->getClientIp();

        $demand = $this->demandeManagerService->findOrCreateDemand($clientIp);

        if (empty($demand)) {
            return $this->redirectToRoute('app_intervention_new');
        }

        if ($demand->getValidatedAt() !== null) {
            return $this->redirectToRoute('app_recap_index');
        }
        return $this->redirectToRoute('app_demande_titre_circulation_edit', ['id' => $demand->getId()]);
    }

    #[Route('/recap', name: 'app_demande_titre_circulation_recap')]
    public function recap(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $demand = $user->getDemandes()->last();

        if (!$demand) {
            throw $this->createNotFoundException('No demand found for recap.');
        }

        $this->demandeManagerService->validateDemand($demand);
        $this->addFlash('demandeValide', 'Votre demande de titre de circulation a bien été pris en compte.');

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_demande_titre_circulation_show', methods: ['GET'])]
    public function show(DemandeTitreCirculation $demandeTitreCirculation): Response
    {
        return $this->render('demande_titre_circulation/show.html.twig', [
            'demande_titre_circulation' => $demandeTitreCirculation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_titre_circulation_edit', methods: ['GET', 'POST'])]
    public function edit(DemandeTitreCirculation $demandeTitreCirculation): Response
    {
        $redirectConditions = $this->demandeManagerService->getRedirectConditions($demandeTitreCirculation);

        foreach ($redirectConditions as $redirect) {
            if ($redirect['condition']) {
                return $this->redirectToRoute($redirect['route'], $redirect['params'], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->redirectToRoute('app_recap_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_demande_titre_circulation_delete', methods: ['POST'])]
    public function delete(Request $request, DemandeTitreCirculation $demandeTitreCirculation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $demandeTitreCirculation->getId(), $request->request->get('_token'))) {
            $this->demandeManagerService->getEntityManager()->remove($demandeTitreCirculation);
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
