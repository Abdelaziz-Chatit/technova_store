<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private CartService $cartService,
        private Security $security
    ) {
    }

    /**
     * Create an order from the current cart
     */
    public function createOrderFromCart(array $customerData): Order
    {
        $cartItems = $this->cartService->getCartWithProducts();

        if (empty($cartItems)) {
            throw new \RuntimeException('Cannot create order from empty cart');
        }

        // Create the order
        $order = new Order();
        
        // Set user if logged in
        $user = $this->security->getUser();
        if ($user) {
            $order->setUser($user);
        }

        // Set customer information
        $order->setCustomerName($customerData['name']);
        $order->setCustomerEmail($customerData['email']);
        $order->setCustomerPhone($customerData['phone'] ?? null);
        $order->setShippingAddress($customerData['address']);
        
        // Set order details
        $order->setStatus('pending');
        $order->setCreatedAt(new \DateTimeImmutable());

        // Calculate total
        $total = 0;

        // Create order items
        foreach ($cartItems as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];

            $orderItem = new OrderItem();
            $orderItem->setOrder($order);
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);
            $orderItem->setPrice($product->getPrice());

            $itemTotal = $product->getPrice() * $quantity;
            $total += $itemTotal;

            $order->addItem($orderItem);
            $this->entityManager->persist($orderItem);
        }

        $order->setTotalAmount($total);

        // Save order
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        // Clear the cart
        $this->cartService->clear();

        return $order;
    }
     /**
     * Mark order as paid and create payment record
     */
    public function markOrderAsPaid(Order $order, string $transactionId): void
    {
        $order->setStatus('paid');

        // Create payment record
        $payment = new Payment();
        $payment->setOrder($order);
        $payment->setMethod('stripe');
        $payment->setStatus('completed');
        $payment->setTransactionId($transactionId);
        $payment->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($payment);
        $this->entityManager->flush();
    }

    /**
     * Cancel order
     */
    public function cancelOrder(Order $order): void
    {
        $order->setStatus('cancelled');
        $this->entityManager->flush();
    }
}