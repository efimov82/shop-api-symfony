<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/v1', name: 'products_api')]
class ProductsController extends AbstractController
{
    #[Route('/products', name: '_list')]
    public function index(ProductRepository $productRepository, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $products = $productRepository->findAll();

        return $this->json($products);
    }

    #[Route('/products/{product}', name: '_details')]
    // #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to access this page')]
    public function getProduct(\App\Entity\Product $product): JsonResponse
    {
        // throw new NotFoundHttpException('test method');
        return $this->json($product);
    }
}
