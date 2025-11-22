<?php

namespace App\Controller;

use App\Service\EntityManagerServices\OrderManagerService;
use App\Service\Payment\Classes\StripeBasketManager;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session/{reference}', name: 'app_stripe_create-session')]
    public function createSession(
        string $reference,
        OrderManagerService $orderManager,
        StripeBasketManager $basketManager
    ): JsonResponse {
        $order = $orderManager->getOrderByReference($reference);
        if (!$order) {
            return new JsonResponse(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $checkoutSession = $basketManager->createCheckoutSession($order, $this->getUser()->getEmail());
            $orderManager->updateStripeSessionId($order, $checkoutSession->id);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['id' => $checkoutSession->id]);
    }
}
