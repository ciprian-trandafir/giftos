<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class CartController extends AbstractController
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    {
        if ((bool)$request->get('ajax') === true) {
            return $this->handleAjax($request);
        }

        $preparedCart = $this->prepareCart($request->getSession()->get('cart_products', []));

        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
            'products' => $preparedCart['products'],
            'totals' => $preparedCart['totals'],
        ]);
    }

    private function handleAjax(Request $request): JsonResponse
    {
        $result = ['success' => false, 'message' => 'Undefined action'];
        $session = $request->getSession();
        $cart = $session->get('cart_products', []);

        switch ($request->get('action')) {
            case 'addToCart':
                $id_product = (int)$request->get('id');
                if (!$id_product) {
                    $result['message'] = 'Invalid product id';
                } else {
                    $session->set('cart_products', $this->addToCartProduct($cart, $id_product));

                    $result = [
                        'success' => true,
                        'message' => 'Success',
                    ];
                }

                break;
            case 'updateProduct':
                $id_product = (int)$request->get('id');
                $qty = (int)$request->get('qty');

                if (!$id_product || !$qty) {
                    $result['message'] = 'Invalid product id or qty';
                } else {
                    $product_index = array_search($id_product, array_column($cart, 'id'), true);
                    $cart[$product_index]['qty'] = $qty;
                    $session->set('cart_products', $cart);

                    $preparedCart = $this->prepareCart($cart);

                    $result = [
                        'success' => true,
                        'message' => 'Success',
                    ];

                    $result['products'] = $this->renderView('cart/_partials/products.html.twig', [
                        'products' => $preparedCart['products'],
                    ]);

                    $result['totals'] = $this->renderView('cart/_partials/totals.html.twig', [
                        'products' => $preparedCart['products'],
                        'totals' => $preparedCart['totals'],
                    ]);
                }

                break;
            case 'deleteProduct':
                $id_product = (int)$request->get('id');

                if (!$id_product) {
                    $result['message'] = 'Invalid product id';
                } else {
                    $product_index = array_search($id_product, array_column($cart, 'id'), true);
                    unset($cart[$product_index]);
                    $session->set('cart_products', array_values($cart));

                    $preparedCart = $this->prepareCart($cart);

                    $result = [
                        'success' => true,
                        'message' => 'Success',
                    ];

                    $result['products'] = $this->renderView('cart/_partials/products.html.twig', [
                        'products' => $preparedCart['products'],
                    ]);

                    $result['totals'] = $this->renderView('cart/_partials/totals.html.twig', [
                        'products' => $preparedCart['products'],
                        'totals' => $preparedCart['totals'],
                    ]);
                }

                break;
        }

        return new JsonResponse($result);
    }

    private function addToCartProduct($cart, $id_product)
    {
        $index = array_search($id_product, array_column($cart, 'id'));
        if ($index !== false) {
            $cart[$index]['qty'] += 1;

            return $cart;
        }

        $cart[] = [
            'id' => $id_product,
            'qty' => 1,
        ];

        return $cart;
    }

    private function prepareCart($cart): array
    {
        // products
        foreach ($cart as &$product) {
            $product['product'] = $this->productRepository->find($product['id']);
            $product['total'] = $product['qty'] * $product['product']->getPrice();
        }
        unset($product);

        // totals
        $total = array_sum(array_column($cart, 'total'));
        $total_products = round($total / (1 + (19 / 100)), 2);
        $total_taxes = $total - $total_products;

        return [
            'products' => $cart,
            'totals' => [
                'products' => $total_products,
                'taxes' => $total_taxes,
                'total' => $total,
            ],
        ];
    }
}
