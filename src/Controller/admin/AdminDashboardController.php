<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Entity\Entreprise;
use App\Entity\ProblemeCarte;
use App\Entity\HistoriqueLogin;
use App\Entity\DemandeTitreCirculation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    #[Route('/admindashboard', name: 'app_admin_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findAll();
        $userCount = $userRepository->count([]);

        $demandeRepository = $entityManager->getRepository(DemandeTitreCirculation::class);
        $demandes = $demandeRepository->findAll();
        $demandesCount = $demandeRepository->count([]);

        $EntrepriseRepository = $entityManager->getRepository(Entreprise::class);
        $EntrepriseCount = $EntrepriseRepository->count([]);

        $countValid = 0;
        $countInvalid = 0;

        foreach ($demandes as $demande) {
            if ($demande->getValidatedAt() !== null) {
                $countValid++;
            } else if ($demande->getValidatedAt() == null) {
                $countInvalid++;
            }
        }

        $problemeCarteRepository = $entityManager->getRepository(ProblemeCarte::class);
        $problemeCarte = $problemeCarteRepository->findAll();

        $EntrepriseRepository = $entityManager->getRepository(Entreprise::class);
        $Entreprise = $EntrepriseRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
            'user' => $user,
            'userCount' => $userCount,
            'demandesCount' => $demandesCount,
            'countValidated' => $countValid,
            'countInvalidated' => $countInvalid,
            'entrepriseCount' => $EntrepriseCount
        ]);
    }

    #[Route('/admindashboard/demande', name: 'app_admin_dashboard_demande')]
    public function dashboardDEmande(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $demandeRepository = $entityManager->getRepository(DemandeTitreCirculation::class);
        $demandes = $demandeRepository->findAll();

        return $this->render('admin/_demande.html.twig', [
            'controller_name' => 'AdminDashboardController',
            'demande' => $demandes,
        ]);
    }

    #[Route('/admindashboard/history', name: 'app_admin_dashboard_history')]
    public function dashboardHistory(EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $historyRepository = $entityManager->getRepository(HistoriqueLogin::class);
        $history = $historyRepository->findAll();

        return $this->render('admin/_history.html.twig', [
            'controller_name' => 'AdminDashboardController',
            'history' => $history,
        ]);
    }
}
