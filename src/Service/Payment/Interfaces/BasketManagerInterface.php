<?php

namespace App\Service\Payment\Interfaces;

interface BasketManagerInterface
{
    public function addProduct(string $productId, int $quantity, float $price): void;

    public function calculateTotal(): float;

    public function checkout(string $currency): string;

    public function confirmPayment(string $paymentId): bool;
}
