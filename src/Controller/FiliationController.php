<?php
namespace App\Controller;

use App\Entity\Filiation;
use App\Entity\User;
use App\Form\FiliationType;
use App\Service\EntityManagerServices\FiliationManagerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/filiation', name: 'app_filiation_')]
class FiliationController extends AbstractController
{
    private readonly FiliationManagerService $filiationManager;

    public function __construct(FiliationManagerService $filiationManager)
    {
        $this->filiationManager = $filiationManager;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $filiations = $this->filiationManager->getAll();
        return $this->render('filiation/index.html.twig', [
            'filiations' => $filiations,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $filiation = new Filiation();
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $this->filiationManager->getUserLatestRequest($user);

        return $this->handleForm($request, $filiation, 'filiation/new.html.twig', $demandeTitreCirculation);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Filiation $filiation): Response
    {
        return $this->render('filiation/show.html.twig', [
            'filiation' => $filiation,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Filiation $filiation): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $demandeTitreCirculation = $this->filiationManager->getUserLatestRequest($user);

        return $this->handleForm($request, $filiation, 'filiation/edit.html.twig', $demandeTitreCirculation);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Filiation $filiation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $filiation->getId(), $request->request->get('_token'))) {
            $this->filiationManager->delete($filiation);
        }

        return $this->redirectToRoute('app_filiation_index');
    }

    private function handleForm(Request $request, Filiation $filiation, string $template, $demandeTitreCirculation): Response
    {
        $form = $this->createForm(FiliationType::class, $filiation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->filiationManager->handleFiliationForm($filiation, $demandeTitreCirculation);
            $nextRoute = $this->filiationManager->determineNextRoute($demandeTitreCirculation);

            return $this->redirectToRoute($nextRoute['name'], $nextRoute['params']);
        }

        return $this->render($template, array_merge(
            ['form' => $form->createView(), 'filiation' => $filiation],
            $this->filiationManager->prepareDemandeDetails($demandeTitreCirculation)
        ));
    }
}
