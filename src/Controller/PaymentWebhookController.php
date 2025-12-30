<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Service\CartService;
use App\Service\OrderService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentWebhookController extends AbstractController
{
    public function __construct(
        private StripeService $stripeService,
        private OrderService $orderService,
        private OrderRepository $orderRepository,
        private CartService $cartService,
        private string $stripeWebhookSecret
    ) {
    }

    #[Route('/webhook/stripe', name: 'app_webhook_stripe', methods: ['POST'])]
    public function handleStripeWebhook(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->headers->get('stripe-signature');

        if (!$signature) {
            return new Response('No signature', Response::HTTP_BAD_REQUEST);
        }

        try {
            $event = $this->stripeService->verifyWebhookSignature(
                $payload,
                $signature,
                $this->stripeWebhookSecret
            );

            // Handle the event
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    $this->handleSuccessfulPayment($session);
                    break;

                case 'checkout.session.expired':
                    $session = $event->data->object;
                    $this->handleExpiredSession($session);
                    break;

                default:
                    // Unhandled event type
                    break;
            }

            return new Response('Webhook handled', Response::HTTP_OK);

        } catch (\Exception $e) {
            return new Response(
                'Webhook error: ' . $e->getMessage(),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route('/payment/success/{orderId}', name: 'app_payment_success')]
    public function success(int $orderId, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($orderId);

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        // Clear cart after successful payment redirect
        $this->cartService->clear();

        $this->addFlash('success', 'Payment successful! Your order is being processed.');

        return $this->redirectToRoute('app_checkout_confirmation', [
            'id' => $order->getId(),
        ]);
    }

    #[Route('/payment/cancel/{orderId}', name: 'app_payment_cancel')]
    public function cancel(int $orderId, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($orderId);

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        // Cancel the order
        $this->orderService->cancelOrder($order);

        $this->addFlash('warning', 'Payment cancelled. Your order has been cancelled.');

        return $this->redirectToRoute('app_cart_index');
    }

    private function handleSuccessfulPayment($session): void
    {
        $orderId = $session->metadata->order_id ?? $session->client_reference_id;

        if (!$orderId) {
            throw new \RuntimeException('No order ID in session metadata');
        }

        $order = $this->orderRepository->find($orderId);

        if (!$order) {
            throw new \RuntimeException('Order not found: ' . $orderId);
        }

        // Mark order as paid
        $this->orderService->markOrderAsPaid($order, $session->payment_intent);
    }

    private function handleExpiredSession($session): void
    {
        $orderId = $session->metadata->order_id ?? $session->client_reference_id;

        if ($orderId) {
            $order = $this->orderRepository->find($orderId);
            if ($order && $order->getStatus() === 'pending_payment') {
                $this->orderService->cancelOrder($order);
            }
        }
    }
}