<?php

namespace App\AdminBundle\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('@Admin/dashboard.html.twig', [
            'title' => 'Admin Dashboard',
        ]);
    }
}
