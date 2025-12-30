<?php

namespace App\Service;

use App\Entity\Order;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
    public function __construct(
        private string $stripeSecretKey,
        private UrlGeneratorInterface $urlGenerator
    ) {
        Stripe::setApiKey($this->stripeSecretKey);
    }

    /**
     * Create a Stripe Checkout Session for an order
     */
    public function createCheckoutSession(Order $order): Session
    {
        $lineItems = [];

        foreach ($order->getItems() as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->getProduct()->getName(),
                        'description' => $item->getProduct()->getDescription() ?? '',
                    ],
                    'unit_amount' => (int) ($item->getPrice() * 100), // Stripe uses cents
                ],
                'quantity' => $item->getQuantity(),
            ];
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => $this->urlGenerator->generate(
                    'app_payment_success',
                    ['orderId' => $order->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'cancel_url' => $this->urlGenerator->generate(
                    'app_payment_cancel',
                    ['orderId' => $order->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
                'client_reference_id' => (string) $order->getId(),
                'customer_email' => $order->getCustomerEmail(),
                'metadata' => [
                    'order_id' => $order->getId(),
                ],
            ]);

            return $session;
        } catch (ApiErrorException $e) {
            throw new \RuntimeException('Stripe API Error: ' . $e->getMessage());
        }
    }

    /**
     * Verify Stripe webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $secret): \Stripe\Event
    {
        try {
            return \Stripe\Webhook::constructEvent($payload, $signature, $secret);
        } catch (\Exception $e) {
            throw new \RuntimeException('Webhook signature verification failed: ' . $e->getMessage());
        }
    }
}