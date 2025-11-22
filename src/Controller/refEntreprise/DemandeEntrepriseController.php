<?php

namespace App\Controller\refEntreprise;

use App\Entity\DemandeTitreCirculation;
use App\Entity\Entreprise;
use App\Entity\Produit;
use App\Entity\User;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DemandeEntrepriseController extends AbstractController
{
    #[Route('/demande/monentreprise', name: 'app_demande_entreprise')]
    public function index(EntityManagerInterface $entityManager, CartService $cart): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        $problemeDeCarte = $user->getProblemecarte();
        $demandeTitreCirculation = $user->getDemandes();
        $panier = $cart->get();

        $EntrepriseRepository = $entityManager->getRepository(Entreprise::class);
        $EntrepriseReferent = $EntrepriseRepository->findByRef($user->getEmail());

        $demandeRepository = $entityManager->getRepository(DemandeTitreCirculation::class);
        $demandeStatusVerif = $demandeRepository->findBy(['status' => DemandeTitreCirculation::STATUS_EMPLOYER_REFERENCE]);

        if ($panier != null) {
            $countPanier = count($panier);
        } else {
            $countPanier = 0;
        }

        if ($panier == null) {
            foreach ($demandeStatusVerif as $demande) {
                $demande->setStatus('Accord');
                $entityManager->flush();
            }
        }

        $demandesDeEntrepriseReferent = [];

        foreach ($EntrepriseReferent as $entreprise) {
            $demandesDeEntreprise = $demandeRepository->findBy(['entreprise' => $entreprise]);
            $demandesDeEntrepriseReferent = array_merge($demandesDeEntrepriseReferent, $demandesDeEntreprise);
        }

        return $this->render('refEntreprise/_demandeMonEntreprise.html.twig', [
            'demandes' => $demandeTitreCirculation,
            'problemeCarte' => $problemeDeCarte,
            'demandeEntreprise' => $demandesDeEntrepriseReferent,
            'countPanier' => $countPanier
        ]);
    }

    #[Route('/demande/monentreprise/statusentreprise/{id}/{parametre}', name: 'app_status_entreprise')]
    public function userMauvaiseEntreprise(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id  = $request->attributes->get('id');
        $parametre = $request->attributes->get('parametre');
        $demandeTitreCirculation = $entityManager->getRepository(DemandeTitreCirculation::class)->find($id);
        $name = 'Carte';
        $produit = $entityManager->getRepository(Produit::class)->findBy(['name' => $name]);
        $idproduit = $produit[0]->getId();


        if ($parametre == 'mauvaisEnt') {
            $demandeTitreCirculation->setEntreprise(null);
            $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_BAD_FIRM);
            $entityManager->flush();
            return $this->redirectToRoute('app_demande_entreprise', [], Response::HTTP_SEE_OTHER);

        } elseif ($parametre == 'bonneEnt') {
            $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_DEPOSIT);
            $entityManager->flush();
            return $this->redirectToRoute('app_demande_entreprise', [], Response::HTTP_SEE_OTHER);
        } elseif ($parametre == 'demandePanier') {
            $demandeTitreCirculation->setStatus(DemandeTitreCirculation::STATUS_AWAITING_PAYMENT);
            $entityManager->flush();
            return $this->redirectToRoute('app_add_to_cart', ['id' => $idproduit, 'demandeId' => $demandeTitreCirculation->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
