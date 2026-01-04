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
        $priceMinRaw = $request->query->get('price_min', '');
        $priceMaxRaw = $request->query->get('price_max', '');
        $priceMin = is_numeric($priceMinRaw) ? (float) $priceMinRaw : null;
        $priceMax = is_numeric($priceMaxRaw) ? (float) $priceMaxRaw : null;
        // Build query with optional filters: search, category, price range
        $qb = $productRepository->createQueryBuilder('p');

        if ($search) {
            $qb->andWhere('p.name LIKE :search OR p.description LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        $selectedCategory = null;
        if ($categoryId) {
            $selectedCategory = $categoryRepository->find($categoryId);
            if ($selectedCategory) {
                $qb->andWhere('p.category = :category')
                   ->setParameter('category', $selectedCategory);
            }
        }

        if ($priceMin !== null) {
            $qb->andWhere('p.price >= :priceMin')
               ->setParameter('priceMin', $priceMin);
        }

        if ($priceMax !== null) {
            $qb->andWhere('p.price <= :priceMax')
               ->setParameter('priceMax', $priceMax);
        }

        $products = $qb->getQuery()->getResult();
        
        $categories = $categoryRepository->findAll();

        return $this->render('product/catalog.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'search' => $search,
            'price_min' => $priceMin,
            'price_max' => $priceMax,
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