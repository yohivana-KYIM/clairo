<?php

namespace App\Controller\sdri;

use App\Entity\MailAppli;
use App\Entity\Entreprise;
use App\Entity\AdresseEntreprise;
use App\Form\AdresseEntrepriseType;
use App\Service\Workflow\Classes\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\AdresseEntrepriseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('sdri/adresse/entreprise')]
class AdresseEntrepriseController extends AbstractController
{
    #[Route('/', name: 'app_adresse_entreprise_index', methods: ['GET'])]
    public function index(AdresseEntrepriseRepository $adresseEntrepriseRepository): Response
    {
        return $this->render('sdri/adresse_entreprise/index.html.twig', [
            'adresse_entreprises' => $adresseEntrepriseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_adresse_entreprise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adresseEntreprise = new AdresseEntreprise();
        $form = $this->createForm(AdresseEntrepriseType::class, $adresseEntreprise);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($adresseEntreprise);
            $entityManager->flush();

            return $this->redirectToRoute('app_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sdri/adresse_entreprise/new.html.twig', [
            'adresse_entreprise' => $adresseEntreprise,
            'form' => $form,
            // 'entreprise' => $id,
        ]);
    }

    #[Route('/{id}/voir', name: 'app_adresse_entreprise_show', methods: ['GET'])]
    public function show(Request $request, AdresseEntreprise $adresseEntreprise): Response
    {
        $id = $request->query->get('id_second');


        return $this->render('sdri/adresse_entreprise/show.html.twig', [
            'adresse_entreprise' => $adresseEntreprise,
            'entreprise' => $id
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/{id}/edit', name: 'app_adresse_entreprise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdresseEntreprise $adresseEntreprise, EntityManagerInterface $entityManager, NotificationService $notificationService): Response
    {
        $form = $this->createForm(AdresseEntrepriseType::class, $adresseEntreprise);
        $id = $request->query->get('id_second');

        $RepoMailAppli = $entityManager->getRepository(MailAppli::class)->find(1);
        $mailApplication = $RepoMailAppli->getEmail();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idAdresseEntreprise = $adresseEntreprise->getId();
            $entreprise = $entityManager->getRepository(Entreprise::class)->find($id) ?? $entityManager->getRepository(Entreprise::class)->find($idAdresseEntreprise);
            $entrepriseNom = $entreprise->getNom();
            $entrepriseRef = $entreprise->getEmailReferent();
            $notificationService->sendTemplatedEmail(
                from: $mailApplication,
                to: $this->getUser()->getEmail(),
                subject: 'FLUXEL : Votre entreprise a été créée avec succès vous pouvez maintenant faire votre demande',
                cc: $entrepriseRef,
                template: 'email_status_titre/base.html.twig',
                templateVars: [
                    'entrepriseNom' => $entrepriseNom,
                ]
            );
            $entityManager->flush();

            return $this->redirectToRoute('app_entreprise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sdri/adresse_entreprise/edit.html.twig', [
            'adresse_entreprise' => $adresseEntreprise,
            'form' => $form,
            'entreprise' => $id,
        ]);
    }

    #[Route('/{id}', name: 'app_adresse_entreprise_delete', methods: ['POST'])]
    public function delete(Request $request, AdresseEntreprise $adresseEntreprise, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adresseEntreprise->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adresseEntreprise);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_adresse_entreprise_index', [], Response::HTTP_SEE_OTHER);
    }
}
