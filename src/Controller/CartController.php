<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    public function __construct(
        private CartService $cartService
    ) {
    }

    #[Route('/', name: 'app_cart_index')]
    public function index(): Response
    {
        $cartItems = $this->cartService->getCartWithProducts();
        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request): Response
    {
        $quantity = $request->request->getInt('quantity', 1);
        
        if ($quantity < 1) {
            $quantity = 1;
        }

        $this->cartService->add($id, $quantity);

        // Handle AJAX requests
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Product added to cart!',
                'cartCount' => $this->cartService->getCount(),
                'cartTotal' => $this->cartService->getTotal(),
            ]);
        }

        $this->addFlash('success', 'Product added to cart!');

        // Redirect back to the previous page or to cart
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(int $id, Request $request): Response
    {
        $quantity = $request->request->getInt('quantity', 1);
        $this->cartService->updateQuantity($id, $quantity);

        // Handle AJAX requests
        if ($request->isXmlHttpRequest()) {
            $cartItems = $this->cartService->getCartWithProducts();
            $itemTotal = 0;
            
            foreach ($cartItems as $item) {
                if ($item['product']->getId() === $id) {
                    $itemTotal = $item['product']->getPrice() * $item['quantity'];
                    break;
                }
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Cart updated!',
                'cartCount' => $this->cartService->getCount(),
                'cartTotal' => $this->cartService->getTotal(),
                'itemTotal' => $itemTotal,
            ]);
        }

        $this->addFlash('success', 'Cart updated!');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(int $id, Request $request): Response
    {
        $this->cartService->remove($id);

        // Handle AJAX requests
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Product removed from cart!',
                'cartCount' => $this->cartService->getCount(),
                'cartTotal' => $this->cartService->getTotal(),
            ]);
        }

        $this->addFlash('success', 'Product removed from cart!');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(Request $request): Response
    {
        $this->cartService->clear();

        // Handle AJAX requests
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'success' => true,
                'message' => 'Cart cleared!',
                'cartCount' => 0,
                'cartTotal' => 0,
            ]);
        }

        $this->addFlash('success', 'Cart cleared!');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/count', name: 'app_cart_count', methods: ['GET'])]
    public function getCount(): JsonResponse
    {
        return new JsonResponse([
            'count' => $this->cartService->getCount(),
            'total' => $this->cartService->getTotal(),
        ]);
    }
}