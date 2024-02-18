<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductRepository;
use App\Repository\TestimonialRepository;

class HomeController extends AbstractController
{
    private $productRepository;
    private $testimonialRepository;

    public function __construct(ProductRepository $productRepository, TestimonialRepository $testimonialRepository)
    {
        $this->productRepository = $productRepository;
        $this->testimonialRepository = $testimonialRepository;
    }

    #[Route('/', name: 'app_home')]
    public function index()
    {
        $products = $this->productRepository->findBy([], ['date_add' => 'DESC'], 8);
        $testimonials = $this->testimonialRepository->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products,
            'testimonials' => $testimonials,
        ]);
    }
}
