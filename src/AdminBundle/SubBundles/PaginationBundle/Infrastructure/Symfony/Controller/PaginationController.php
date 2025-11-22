<?php

namespace App\AdminBundle\SubBundles\PaginationBundle\Infrastructure\Symfony\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaginationController extends AbstractController
{
    #[Route('/pagination', name: 'pagination')]
    public function paginate(): Response
    {
        return $this->render('@Pagination/bootstrap/pagination.html.twig');
    }
}
