<?php

namespace App\Service\EntityManagerServices;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class OrderManagerService
{
    private readonly OrderRepository $orderRepository;
    private readonly EntityManagerInterface $em;

    public function __construct(OrderRepository $orderRepository, EntityManagerInterface $em)
    {
        $this->orderRepository = $orderRepository;
        $this->em = $em;
    }

    public function getOrderByReference(string $reference): ?Order
    {
        return $this->orderRepository->findOneByReference(['reference' => $reference]);
    }

    public function updateStripeSessionId(Order $order, string $sessionId): void
    {
        $order->setStripeSessionId($sessionId);
        $this->em->flush();
    }
}