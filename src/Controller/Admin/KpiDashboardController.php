<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class KpiDashboardController extends AbstractController
{
    #[Route('/admin/kpi', name: 'admin_kpi_dashboard')]
    public function index(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        ProductRepository $productRepository
    ): Response {
        // Calculate KPIs
        $totalRevenue = $orderRepository->getTotalRevenue();
        $totalOrders = $orderRepository->count([]);
        $averageOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // Get last 30 days data for charts
        $revenueData = $orderRepository->getRevenueByDate(30);
        $ordersData = $orderRepository->getOrdersByDate(30);

        // Top 5 products by revenue
        $topProducts = $orderItemRepository->getTopProductsByRevenue(5);

        // Order status breakdown
        $statusBreakdown = $orderRepository->getStatusBreakdown();

        // Recent orders (last 10)
        $recentOrders = $orderRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            10
        );

        return $this->render('admin/kpi_dashboard.html.twig', [
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'averageOrderValue' => $averageOrderValue,
            'revenueData' => $revenueData,
            'ordersData' => $ordersData,
            'topProducts' => $topProducts,
            'statusBreakdown' => $statusBreakdown,
            'recentOrders' => $recentOrders,
        ]);
    }
}

