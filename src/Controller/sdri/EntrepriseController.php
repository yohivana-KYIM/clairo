<?php

namespace App\Controller\sdri;

use App\Entity\Entreprise;
use App\Entity\AdresseEntreprise;
use App\Form\EntrepriseType;
use App\Service\EntityManagerServices\UserManagerService;
use App\Service\EntityManagerServices\EnterpriseManagerService;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/sdri/entreprise')]
class EntrepriseController extends AbstractController
{
    private readonly UserManagerService $UserManagerService;
    private readonly EnterpriseManagerService $entrepriseService;

    public function __construct(
        UserManagerService $UserManagerService,
        EnterpriseManagerService $entrepriseService
    ) {
        $this->UserManagerService = $UserManagerService;
        $this->entrepriseService = $entrepriseService;
    }

    #[Route('/', name: 'app_entreprise_index', methods: ['GET'])]
    public function index(): Response
    {
        $entreprises = $this->entrepriseService->getAllEntreprises();

        return $this->render('sdri/entreprise/index.html.twig', [
            'entreprises' => $entreprises,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws TransportExceptionInterface
     */
    #[Route('/new', name: 'app_entreprise_new', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $entreprise = new Entreprise();
        $adresse = new AdresseEntreprise();
        $form = $this->createForm(EntrepriseType::class, $entreprise);

        $lastRoute = $this->getLastRoute($request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existing = null;
            if ($entreprise->getSiren()) {
                $existing = $this->entrepriseService->findOneBySiren($entreprise->getSiren());
            }
            if ($existing) {
                $entreprise->setEntrepriseMere($existing);
                $this->addFlash('info', "Cette entreprise a été rattachée à l'entité principale : " . $existing->getNom());
                $this->UserManagerService->notifyReferent($existing, $entreprise);
            } else {
                $existingEntreprise = $this->entrepriseService->findOneBySiret($entreprise->getSiret());

                if ($existingEntreprise) {
                    if ($existingEntreprise->getTvaIntraCommunautaire() !== $entreprise->getTvaIntraCommunautaire()) {
                        $entreprise->setEntrepriseMere($existingEntreprise);
                        $this->UserManagerService->notifyReferent($existingEntreprise, $entreprise);

                        $this->addFlash('info', 'Cette entité a été liée comme filiale à l’entreprise mère existante.');
                    } else {
                        $this->addFlash('error', 'Une entreprise avec ce SIRET et cette TVA existe déjà.');
                        return $this->render('sdri/entreprise/new.html.twig', [
                            'entreprise' => $entreprise,
                            'form' => $form,
                            'lastRoute' => $lastRoute,
                        ]);
                    }
                }
            }
            $emails = [
                'referent' => $form->get('emailReferent')->getData(),
                'suppleant1' => $form->get('suppleant1')->getData(),
                'suppleant2' => $form->get('suppleant2')->getData(),
            ];

            $this->UserManagerService->manageUsers($emails, $entreprise);
            $this->entrepriseService->createEntreprise($entreprise, $adresse);

            return $this->redirectToRoute('app_adresse_entreprise_edit', [
                'id' => $adresse->getId(),
                'id_second' => $entreprise->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sdri/entreprise/new.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form->createView(),
            'lastRoute' => $lastRoute,
        ]);
    }

    #[Route('/{id}', name: 'app_entreprise_show', methods: ['GET'])]
    public function show(Request $request, Entreprise $entreprise): Response
    {
        $lastRoute = $this->getLastRoute($request);

        return $this->render('sdri/entreprise/show.html.twig', [
            'entreprise' => $entreprise,
            'lastRoute' => $lastRoute,
            'idAdresseEntreprise' => $entreprise?->getAdresse()?->getId(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_entreprise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entreprise $entreprise): Response
    {
        $form = $this->createForm(EntrepriseType::class, $entreprise);

        $lastRoute = $this->getLastRoute($request);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emails = [
                'referent' => $form->get('emailReferent')->getData(),
                'suppleant1' => $form->get('suppleant1')->getData(),
                'suppleant2' => $form->get('suppleant2')->getData(),
            ];

            $this->UserManagerService->updateUsers($emails, $entreprise);
            $this->entrepriseService->updateEntreprise($entreprise);

            return $this->redirectToRoute('app_adresse_entreprise_edit', [
                'id' => $entreprise->getAdresse()->getId(),
                'id_second' => $entreprise->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sdri/entreprise/edit.html.twig', [
            'entreprise' => $entreprise,
            'form' => $form->createView(),
            'lastRoute' => $lastRoute,
        ]);
    }

    #[Route('/{id}', name: 'app_entreprise_delete', methods: ['POST'])]
    public function delete(Request $request, Entreprise $entreprise): Response
    {
        if ($this->isCsrfTokenValid('delete' . $entreprise->getId(), $request->request->get('_token'))) {
            $this->entrepriseService->deleteEntreprise($entreprise);
        }

        return $this->redirectToRoute('app_entreprise_index', [], Response::HTTP_SEE_OTHER);
    }

    private function getLastRoute(Request $request): ?string
    {
        return basename((string) $request->headers->get('referer', ''));
    }
}
