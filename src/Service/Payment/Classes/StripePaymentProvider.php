<?php

namespace App\Service\Payment\Classes;

use App\Service\Payment\Interfaces\PaymentProviderInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripePaymentProvider implements PaymentProviderInterface
{

    public function __construct(private readonly StripeClient $client)
    {
    }

    /**
     * @throws ApiErrorException
     */
    public function createPaymentIntent(float $amount, string $currency, ?array $metadata = []): string
    {
        $paymentIntent = $this->client->paymentIntents->create([
            'amount' => $amount * 100,
            'currency' => $currency,
            'metadata' => $metadata,
        ]);

        return $paymentIntent->id;
    }

    /**
     * @throws ApiErrorException
     */
    public function capturePayment(string $paymentId): bool
    {
        $this->client->paymentIntents->capture($paymentId);
        return true;
    }

    /**
     * @throws ApiErrorException
     */
    public function refundPayment(string $paymentId, ?float $amount = 0): bool
    {
        $this->client->refunds->create([
            'payment_intent' => $paymentId,
            'amount' => $amount ? $amount * 100 : null,
        ]);
        return true;
    }

    public function getClient(): StripeClient
    {
        return $this->client;
    }
}
