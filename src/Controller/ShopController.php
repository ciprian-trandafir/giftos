<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;

class ShopController extends AbstractController
{
    private $categoryRepository;
    private $productRepository;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    #[Route('/shop', name: 'app_shop')]
    public function index(): Response
    {
        $categories = $this->categoryRepository->findBy([], ['name' => 'ASC']);
        $products =  $this->productRepository->findBy([], ['date_add' => 'DESC']);

        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
