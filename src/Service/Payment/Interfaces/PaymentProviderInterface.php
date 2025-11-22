<?php

namespace App\Service\Payment\Interfaces;

interface PaymentProviderInterface
{

    public function createPaymentIntent(float $amount, string $currency, ?array $metadata = []): string;

    public function capturePayment(string $paymentId): bool;

    public function refundPayment(string $paymentId, ?float $amount = null): bool;
}