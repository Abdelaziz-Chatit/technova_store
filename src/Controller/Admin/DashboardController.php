<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_RESPONSABLE')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Show EasyAdmin dashboard for both ADMIN and RESPONSABLE users
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('TechNova Store Admin')
            ->setFaviconPath('favicon.ico')
            ->setTranslationDomain('admin');
    }

    public function configureMenuItems(): iterable
    {
        // Show Dashboard button only for managers (RESPONSABLE users without ADMIN role)
        if ($this->isGranted('ROLE_RESPONSABLE') && !$this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToRoute('Dashboard', 'fa fa-home', 'admin');
        }

        // Product Management (for RESPONSABLE and ADMIN)
        if ($this->isGranted('ROLE_RESPONSABLE')) {
            yield MenuItem::section('Product Management');
            yield MenuItem::linkToCrud('Products', 'fa fa-box', \App\Entity\Product::class);
            yield MenuItem::linkToCrud('Categories', 'fa fa-tags', \App\Entity\Category::class);
            
            yield MenuItem::section('Marketing');
            yield MenuItem::linkToCrud('Advertisements', 'fa fa-image', \App\Entity\Advertisement::class);
        }

        // Admin-only features
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Administration');
            yield MenuItem::linkToRoute('KPI Dashboard', 'fa fa-chart-line', 'admin');
            yield MenuItem::linkToCrud('Orders', 'fa fa-shopping-cart', \App\Entity\Order::class);
            yield MenuItem::linkToCrud('Users', 'fa fa-users', \App\Entity\User::class);
        }

        yield MenuItem::section('Back to Site');
        yield MenuItem::linkToRoute('View Store', 'fa fa-store', 'app_home');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}

