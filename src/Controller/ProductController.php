<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product_catalog')]
    public function catalog(
        Request $request,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $search = $request->query->get('search', '');
        $categoryId = $request->query->get('category', '');
        
        if ($search) {
            // Search by name and description
            $products = $productRepository->createQueryBuilder('p')
                ->where('p.name LIKE :search OR p.description LIKE :search')
                ->setParameter('search', '%' . $search . '%')
                ->getQuery()
                ->getResult();
        } elseif ($categoryId) {
            // Filter by category
            $products = $productRepository->findBy(['category_id' => $categoryId]);
        } else {
            $products = $productRepository->findAll();
        }
        
        $categories = $categoryRepository->findAll();

        return $this->render('product/catalog.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => null,
            'search' => $search,
        ]);
    }

    #[Route('/products/category/{slug}', name: 'app_product_by_category')]
    public function byCategory(
        string $slug,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository
    ): Response {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $products = $productRepository->findBy(['category' => $category]);
        $categories = $categoryRepository->findAll();

        return $this->render('product/catalog.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $category,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }
}