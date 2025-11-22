<?php

namespace App\Controller\refEntreprise;

use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RefEntrepriseController extends AbstractController
{
    #[Route('/refentreprise', name: 'app_ref_entreprise')]
    public function RefEntreprise(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $entreprise = null;
        $EntrepriseRepository = $entityManager->getRepository(Entreprise::class);
        $entrepriseRef = $EntrepriseRepository->findByRef($user->getEmail());
        if ($entrepriseRef) {
            $entreprise = $entrepriseRef[0]?->getNom();
        }

        return $this->render('refEntreprise/home_refEntreprise.html.twig', [
            'controller_name' => 'RefEntrepriseController',
            'entreprise' => $entreprise,
        ]);
    }
}
