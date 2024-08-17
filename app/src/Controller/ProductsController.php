<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        $limit = $request->query->getInt('limit', 10);

        $products = $productRepository->getPaginatedProducts($page, $limit);
        $total = count($productRepository->findAll());

        return $this->json(
            $products,
            Response::HTTP_OK,
            [
                "x-total-items" => $total
            ]
        );
    }

    #[Route('/products/{product}', name: '_details')]
    // #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to access this page')]
    public function getProduct(\App\Entity\Product $product): JsonResponse
    {
        // throw new NotFoundHttpException('test method');
        return $this->json($product);
    }
}
