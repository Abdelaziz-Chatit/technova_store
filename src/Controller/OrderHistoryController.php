<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class OrderHistoryController extends AbstractController
{
    #[Route('/profile/orders', name: 'app_order_history')]
    public function index(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('profile/order_history.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/profile/orders/{id}', name: 'app_order_detail')]
    public function show(int $id, OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $order = $orderRepository->find($id);

        // Check if order exists and belongs to the current user
        if (!$order || $order->getUser() !== $user) {
            throw $this->createNotFoundException('Order not found');
        }

        return $this->render('profile/order_detail.html.twig', [
            'order' => $order,
        ]);
    }
}

