<?php

namespace App\Controller\refEntreprise;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/commande/erreur/{stripeSessionId}', name: 'app_order_cancel')]
    public function index($stripeSessionId): Response
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['StripeSessionId' => $stripeSessionId]);
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

    // envoie de mail à l'utilisateur

    return $this->render('refEntreprise/order_cancel/index.html.twig',[
        'order' => $order
    ]);
}
}