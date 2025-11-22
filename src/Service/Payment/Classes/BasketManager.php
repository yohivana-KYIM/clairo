<?php

namespace App\Service\Payment\Classes;

use App\Service\Payment\Interfaces\BasketManagerInterface;
use App\Service\Payment\Interfaces\PaymentProviderInterface;


class BasketManager implements BasketManagerInterface
{
    protected PaymentProviderInterface $paymentProvider;

    protected array $basket = [];

    public function __construct(PaymentProviderInterface $paymentProvider)
    {
        $this->paymentProvider = $paymentProvider;
    }

    public function addProduct(string $productId, int $quantity, float $price): void
    {
        $this->basket[$productId] = [
            'quantity' => $quantity,
            'price' => $price,
        ];
    }

    public function calculateTotal(): float
    {
        return array_reduce($this->basket, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0.0);
    }

    public function getBasket(): array
    {
        return $this->basket;
    }

    public function clearBasket(): void
    {
        $this->basket = [];
    }

    public function checkout(string $currency): string
    {
        $total = $this->calculateTotal();
        return $this->paymentProvider->createPaymentIntent($total, $currency);
    }

    public function confirmPayment(string $paymentId): bool
    {
        return $this->paymentProvider->capturePayment($paymentId);
    }
}

