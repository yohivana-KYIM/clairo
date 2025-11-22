<?php

namespace App\Controller\refEntreprise;

use App\Entity\Order;
use App\Entity\Entreprise;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MesCommandesController extends AbstractController
{
    #[Route('/mescommandes', name: 'app_mes_commandes')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $EntrepriseRepository = $entityManager->getRepository(Entreprise::class);
        $EntrepriseReferent = $EntrepriseRepository->findByRef($user->getEmail());

        $OrderRepository = $entityManager->getRepository(Order::class);
        $OrderReferent = $OrderRepository->findBy(['User' => $user]);

        $OrderDetailsRepository = $entityManager->getRepository(OrderDetails::class);

        $paidOrderDetails = [];

        foreach ($OrderReferent as $order) {
            if ($order->getIspaid()) {
                $orderDetails = $OrderDetailsRepository->findBy(['myOrder' => $order]);
                $totalPrice = 0;
                $totalQuantity = 0;

                foreach ($orderDetails as $orderDetail) {
                    $totalPrice += $orderDetail->getPrice();
                    $totalQuantity += $orderDetail->getQuantity();
                }

                $paidOrderDetails[$order->getId()] = [
                    'order' => $order,
                    'orderDetails' => $orderDetails,
                    'totalPrice' => $totalPrice,
                    'totalQuantity' => $totalQuantity,
                ];
            }
        }

        return $this->render('refEntreprise/mes_commandes/index.html.twig', [
            'controller_name' => 'MesCommandesController',
            'EntrepriseReferent' => $EntrepriseReferent,
            'Order' => $OrderReferent,
            'paidOrderDetails' => $paidOrderDetails,
        ]);
    }
}
