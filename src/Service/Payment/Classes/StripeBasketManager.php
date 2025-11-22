<?php

namespace App\Service\Payment\Classes;

use App\Entity\Order;
use App\Service\Payment\Interfaces\PaymentProviderInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripeBasketManager extends BasketManager
{
    private readonly string $domain;

    public function __construct(PaymentProviderInterface $paymentProvider, string $domain)
    {
        parent::__construct($paymentProvider);
        $this->domain = $domain;
    }

    /**
     * Build the basket from an Order.
     *
     * @param Order $order
     */
    public function populateBasketFromOrder(Order $order): void
    {
        $this->clearBasket();

        foreach ($order->getOrderDetails() as $detail) {
            $this->addProduct(
                $detail->getId(),
                $detail->getQuantity(),
                $detail->getPrice()
            );
        }
    }

    /**
     * Build line items for Stripe from the current basket.
     *
     * @return array
     */
    public function buildLineItems(): array
    {
        $lineItems = [];
        foreach ($this->getBasket() as $productId => $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $item['price'],
                    'product_data' => [
                        'name' => $productId,
                    ],
                ],
                'quantity' => $item['quantity'],
            ];
        }

        return $lineItems;
    }

    /**
     * Create a Stripe checkout session from an Order.
     *
     * @param Order $order
     * @param string $customerEmail
     * @return Session
     * @throws ApiErrorException
     */
    public function createCheckoutSession(Order $order, string $customerEmail): Session
    {
        // Populate the basket from the order
        $this->populateBasketFromOrder($order);

        // Build line items for Stripe
        $lineItems = $this->buildLineItems();

        // Create the Stripe session via client
        return $this->paymentProvider->getClient()->checkout->sessions->create([
            'customer_email' => $customerEmail,
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => "{$this->domain}/commande/merci/{CHECKOUT_SESSION_ID}",
            'cancel_url' => "{$this->domain}/commande/erreur/{CHECKOUT_SESSION_ID}",
        ]);
    }
}