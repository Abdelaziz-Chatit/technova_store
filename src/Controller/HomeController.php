<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\AdvertisementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        CategoryRepository $categoryRepository,
        AdvertisementRepository $advertisementRepository
    ): Response
    {
        $categories = $categoryRepository->findAll();
        $totalProducts = 0;
        $advertisements = $advertisementRepository->findActiveAdvertisements();
        
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'totalProducts' => $totalProducts,
            'advertisements' => $advertisements,
        ]);
    }
}