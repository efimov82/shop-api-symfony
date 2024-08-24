<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

use App\Entity\Product;
use App\Repository\ProductRepository;

#[Route('/api/v1', name: 'products_api')]
#[OA\Tag(name: 'Products API')]
class ProductsController extends AbstractController
{
    #[Route('/products', name: '_list')]
    #[OA\Parameter(
        name: 'page',
        description: 'Page number',
        in: 'query',
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Number items per page',
        in: 'query',
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Get products.',

        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(new Model(type: Product::class)),
        )
    )]
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

    #[Route('/products/{id}', name: '_details')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Get product by ID.',
        content: new Model(type: Product::class)
    )]
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json($product);
    }
}
