<?php

namespace App\Controller\refEntreprise;

use App\Entity\Entreprise;
use App\Entity\AdresseFacturation;
use App\Form\AdresseFacturationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AdresseFacturationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/adresse/facturation')]
class AdresseFacturationController extends AbstractController
{
    #[Route('/', name: 'app_adresse_facturation_index', methods: ['GET'])]
    public function index(AdresseFacturationRepository $adresseFacturationRepository): Response
    {
        return $this->render('adresse_facturation/index.html.twig', [
            'adresse_facturations' => $adresseFacturationRepository->findAll(),
        ]);
    }

    #[Route('/new/{entrepriseName}/{reference}', name: 'app_adresse_facturation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, $entrepriseName, $reference): Response
    {
        $adresseFacturation = new AdresseFacturation();
        $form = $this->createForm(AdresseFacturationType::class, $adresseFacturation);
        $form->handleRequest($request);
        $entreprise = $entityManager->getRepository(Entreprise::class)->findOneBy(['id' => $entrepriseName]);

        $entrepriseName = $entreprise->getNom();

        if ($form->isSubmitted() && $form->isValid()) {
            $adresseFacturation->setAdresseFacturationEntreprise($entreprise);
            $entityManager->persist($adresseFacturation);
            $entityManager->flush();

            return $this->redirectToRoute('app_order_recap', ['reference' => $reference], Response::HTTP_SEE_OTHER);
        }

        return $this->render('adresse_facturation/new.html.twig', [
            'adresse_facturation' => $adresseFacturation,
            'form' => $form,
            'entrepriseName' => $entrepriseName,
            'reference' => $reference,
        ]);
    }

    #[Route('/{id}', name: 'app_adresse_facturation_show', methods: ['GET'])]
    public function show(AdresseFacturation $adresseFacturation): Response
    {
        return $this->render('adresse_facturation/show.html.twig', [
            'adresse_facturation' => $adresseFacturation,
        ]);
    }

    #[Route('/{id}/{entrepriseName}/{reference}/edit', name: 'app_adresse_facturation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdresseFacturation $adresseFacturation, EntityManagerInterface $entityManager, $entrepriseName, $reference): Response
    {
        $form = $this->createForm(AdresseFacturationType::class, $adresseFacturation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_order_recap', ['reference' => $reference], Response::HTTP_SEE_OTHER);
        }

        return $this->render('adresse_facturation/edit.html.twig', [
            'adresse_facturation' => $adresseFacturation,
            'form' => $form,
            'entrepriseName' => $entrepriseName,
            'reference' => $reference,
        ]);
    }

    #[Route('/{id}', name: 'app_adresse_facturation_delete', methods: ['POST'])]
    public function delete(Request $request, AdresseFacturation $adresseFacturation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adresseFacturation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adresseFacturation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_adresse_facturation_index', [], Response::HTTP_SEE_OTHER);
    }
}
