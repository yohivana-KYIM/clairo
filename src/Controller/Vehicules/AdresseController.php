<?php

namespace App\Controller\Vehicules;

use App\Entity\Adresse;
use App\Entity\User;
use App\Form\AdresseType;
use App\Repository\AdresseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/vehicule_adresse')]
class AdresseController extends AbstractController
{
    #[Route('/', name: 'app_vehicule_adresse_index', methods: ['GET'])]
    public function index(AdresseRepository $adresseRepository): Response
    {
        return $this->render('vehicule_adresse/index.html.twig', [
            'adresses' => $adresseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_vehicule_adresse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adresse = new Adresse();
        $form = $this->createForm(AdresseType::class, $adresse);
        $user = $this->getUser();
        assert($user instanceof User);

        $demandeTitreCirculation = $user->getDemandeVehicules()->last();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adresse->setSubmited(true);

            $entityManager->persist($adresse);
            $entityManager->flush();
            $demandeTitreCirculation->setAdresse($adresse);
            $entityManager->persist($demandeTitreCirculation);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_info_complementaire_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule_adresse/new.html.twig', [
            'demande' => $demandeTitreCirculation,
            'form' => $form,
            'intervention' => $demandeTitreCirculation->getIntervention(),
            'filiation' => $demandeTitreCirculation->getFiliation(),
            'adresse' => $demandeTitreCirculation->getAdresse(),
            'etatCivil' => $demandeTitreCirculation->getEtatCivil(),
            'infoComplementaire' => $demandeTitreCirculation->getInfocomplementaire(),
            'documentPersonnel' => $demandeTitreCirculation->getDocpersonnel(),
            'documentProfessionnel' => $demandeTitreCirculation->getDocumentprofessionnel(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_adresse_show', methods: ['GET'])]
    public function show(Adresse $adresse): Response
    {
        return $this->render('vehicule_adresse/show.html.twig', [
            'adresse' => $adresse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_vehicule_adresse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Adresse $adresse, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdresseType::class, $adresse);
        $user = $this->getUser();
        assert($user instanceof User);

        $demandeTitreCirculation = $user->getDemandeVehicules()->last();
        $infoComplementaire =  $demandeTitreCirculation->getInfocomplementaire();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            if ($infoComplementaire) {
                return $this->redirectToRoute('app_vehicule_info_complementaire_edit', ['id' => $infoComplementaire ->getId()], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute('app_vehicule_info_complementaire_new', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('vehicule_adresse/edit.html.twig', [
            'demande' => $demandeTitreCirculation,
            'form' => $form,
            'intervention' => $demandeTitreCirculation->getIntervention(),
            'adresse' => $demandeTitreCirculation->getAdresse(),
            'etatCivil' => $demandeTitreCirculation->getEtatCivil(),
            'infoComplementaire' => $demandeTitreCirculation->getInfocomplementaire(),
            'documentPersonnel' => $demandeTitreCirculation->getDocpersonnel(),
            'documentProfessionnel' => $demandeTitreCirculation->getDocumentprofessionnel(),
        ]);
    }

    #[Route('/{id}', name: 'app_vehicule_adresse_delete', methods: ['POST'])]
    public function delete(Request $request, Adresse $adresse, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adresse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adresse);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicule_adresse_index', [], Response::HTTP_SEE_OTHER);
    }
}


