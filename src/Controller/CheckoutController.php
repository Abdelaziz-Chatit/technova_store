<?php

namespace App\Controller;

use App\Form\CheckoutType;
use App\Repository\OrderRepository;
use App\Service\CartService;
use App\Service\OrderService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/checkout')]
class CheckoutController extends AbstractController
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
        private StripeService $stripeService
    ) {
    }

    #[Route('/', name: 'app_checkout_index')]
    public function index(Request $request): Response
    {
        // Check if cart is empty
        $cartItems = $this->cartService->getCartWithProducts();
        
        if (empty($cartItems)) {
            $this->addFlash('warning', 'Your cart is empty!');
            return $this->redirectToRoute('app_product_catalog');
        }

        $form = $this->createForm(CheckoutType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Create order
                $customerData = $form->getData();
                $order = $this->orderService->createOrderFromCart($customerData);

                // Create Stripe Checkout Session
                $session = $this->stripeService->createCheckoutSession($order);

                // Redirect to Stripe Checkout
                return $this->redirect($session->url);

            } catch (\Exception $e) {
                $this->addFlash('error', 'Failed to process checkout: ' . $e->getMessage());
            }
        }

        return $this->render('checkout/index.html.twig', [
            'form' => $form,
            'cartItems' => $cartItems,
            'total' => $this->cartService->getTotal(),
            'stripePublicKey' => $this->getParameter('stripe_public_key'),
        ]);
    }

    #[Route('/confirmation/{id}', name: 'app_checkout_confirmation')]
    public function confirmation(int $id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        return $this->render('checkout/confirmation.html.twig', [
            'order' => $order,
        ]);
    }
}