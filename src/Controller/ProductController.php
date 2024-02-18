<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route('/product/{id}-{slug}', name: 'app_product')]
    public function index(Request $request): Response
    {
        $product = $this->productRepository->find($request->get('id'));

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        if ($product->getSlug() !== $request->get('slug')) {
            return $this->redirectToRoute('app_product', ['id' => $product->getId(), 'slug' => $product->getSlug()]);
        }

        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'product' => $product,
        ]);
    }
}
