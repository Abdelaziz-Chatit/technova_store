<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const CART_SESSION_KEY = 'cart';

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository
    ) {
    }

    /**
     * Add a product to the cart
     */
    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $this->saveCart($cart);
    }

    /**
     * Remove a product from the cart
     */
    public function remove(int $productId): void
    {
        $cart = $this->getCart();

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->saveCart($cart);
        }
    }

    /**
     * Update product quantity in cart
     */
    public function updateQuantity(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($productId);
            return;
        }

        $cart = $this->getCart();
        $cart[$productId] = $quantity;
        $this->saveCart($cart);
    }

    /**
     * Get cart items with full product details
     * Returns: [['product' => Product, 'quantity' => int], ...]
     */
    public function getCartWithProducts(): array
    {
        $cart = $this->getCart();
        $cartWithProducts = [];

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepository->find($productId);

            if ($product) {
                $cartWithProducts[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                ];
            } else {
                // Product no longer exists, remove from cart
                $this->remove($productId);
            }
        }

        return $cartWithProducts;
    }

    /**
     * Calculate cart total
     */
    public function getTotal(): float
    {
        $total = 0;
        $cartWithProducts = $this->getCartWithProducts();

        foreach ($cartWithProducts as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        return $total;
    }

    /**
     * Get total number of items in cart
     */
    public function getCount(): int
    {
        $cart = $this->getCart();
        return array_sum($cart);
    }

    /**
     * Clear the entire cart
     */
    public function clear(): void
    {
        $this->saveCart([]);
    }

    /**
     * Get raw cart array from session
     * Returns: [productId => quantity, ...]
     */
    private function getCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get(self::CART_SESSION_KEY, []);
    }

    /**
     * Save cart to session
     */
    private function saveCart(array $cart): void
    {
        $session = $this->requestStack->getSession();
        $session->set(self::CART_SESSION_KEY, $cart);
    }
}