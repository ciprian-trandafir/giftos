<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

class CategoryController extends AbstractController
{
    private $categoryRepository;
    private $productRepository;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    #[Route('/category/{id}-{slug}', name: 'app_category')]
    public function index(int $id, string $slug): Response
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        if ($category->getSlug() !== $slug) {
            return $this->redirectToRoute('app_category', ['id' => $category->getId(), 'slug' => $category->getSlug()]);
        }

        $products = $this->productRepository->findProductsByCategory($id);

        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $category,
            'products' => $products,
        ]);
    }
}
