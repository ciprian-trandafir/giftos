<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Testimonial;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\TestimonialRepository;
use App\Repository\UserRepository;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    private $categoryRepository;
    private $productRepository;
    private $testimonialRepository;
    private $userRepository;

    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository, TestimonialRepository $testimonialRepository, UserRepository $userRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->testimonialRepository = $testimonialRepository;
        $this->userRepository = $userRepository;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();
        $categories = $this->categoryRepository->findAll();
        $testimonials = $this->testimonialRepository->findAll();
        $users = $this->userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'products_count' => count($products),
            'categories_count' => count($categories),
            'testimonials_count' => count($testimonials),
            'users_count' => count($users),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Giftos Admin Panel')
            ->setFaviconPath('assets/all/img/favicon.png');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Products', 'fas fa-box', Product::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Testimonials', 'fas fa-comment', Testimonial::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getName())
            ->setGravatarEmail($user->getEmail())
            ->addMenuItems([
                MenuItem::linkToRoute('Home', 'fa fa-house', 'app_home'),
            ]);
    }
}
