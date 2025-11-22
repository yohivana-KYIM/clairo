<?php

namespace App\Service\Pdf;

use App\Entity\EntrepriseUnifiee;
use App\Entity\Order;
use App\Entity\Entreprise;
use App\Repository\EntrepriseUnifieeRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PdfGeneratorService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly Pdf $snappy,
        private readonly EntrepriseUnifieeRepository $entrepriseUnifieeRepository
    )
    {
    }

    public function preparePdfData(int $orderId): array
    {
        $order = $this->entityManager->getRepository(Order::class)->find($orderId);

        if (!$order) {
            throw new InvalidArgumentException("Order with ID $orderId not found.");
        }

        $userOrderEmail = $order->getUser()->getEmail();
        $entreprise = $this->entrepriseUnifieeRepository->findOneByEmailReferentOrSuppliant($userOrderEmail);

        if (!$entreprise) {
            throw new InvalidArgumentException("Entreprise not found for user email $userOrderEmail.");
        }

        $adresseEntreprise = $entreprise->getAdresseObject();
        $totalPrice = 0;
        $totalQuantity = 0;
        $orderDetailsArray = [];

        foreach ($order->getOrderDetails() as $orderDetail) {
            $price = $orderDetail->getPrice();
            $quantity = $orderDetail->getQuantity();

            $totalPrice += $price * $quantity;
            $totalQuantity += $quantity;

            $orderDetailsArray[] = [
                'details' => $orderDetail,
            ];
        }

        $orderTotal = [
            'totalPrice' => $totalPrice,
            'totalQuantity' => $totalQuantity,
        ];

        return [
            'orderDetailsArrays' => $orderDetailsArray,
            'orderTotal' => $orderTotal,
            'entreprise' => $entreprise,
            'adresseEntreprise' => $adresseEntreprise,
        ];
    }

    public function generatePdfFromHtml(string $html, string $filename): Response
    {
        return new Response(
            $this->snappy->getOutputFromHtml($html),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '.pdf"',
            ]
        );
    }
}
