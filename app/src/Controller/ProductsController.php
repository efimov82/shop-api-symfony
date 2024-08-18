<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
// use Nelmio\ApiDocBundle\Annotation\Model;
// use Nelmio\ApiDocBundle\Annotation\Security;

//use Swagger\Annotations as OA;
// use OpenApi\Attributes as OA;
use OpenApi\Attributes as OA;
// use Nelmio\ApiDocBundle\Annotations as OA;

#[Route('/api/v1', name: 'products_api')]
#[OA\Tag(name: 'Products API')]
class ProductsController extends AbstractController
{
    #[Route('/products', name: '_list')]
    /*
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *     example={
     *      "file": "file",
     *      "documentCategoryCode": "O"
     *     },
     *     @OA\Schema(
     *      type="object",
     *      @OA\Property(
     *          property="file",
     *          required=true,
     *          type="file",
     *          description="File to be uploaded",
     *          example="file"
     *      ),
     *      @OA\Property(
     *          property="documentCategoryCode",
     *          required=true,
     *          type="string",
     *          description="Document category")
     *      ),
     *     )
     *  )
     * )
     * @OQ\Response(array[Product])
     */

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
    // #[OA\RequestBody(
    //     required: true,
    //     content: new OA\JsonContent(
    //         type: Object::class,
    //         example: [
    //             "status" => "status",
    //             "comment" => "comment"
    //         ]
    //     )
    // )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Get products.',

        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Product::class)),
            // items: new OA\Items(ref: Model(type: Product::class)),
            // items: new Model(type: Product::class),
            // items: new OA\Items(ref: '#components/entity/Product'),
            //ref: new OA\Items(type: Product::class),
            example: [
                new OA\Items(new Model(type: Product::class),
                     // (ref: new Model(type: Product::class)),
                    // type: Product::class,
                    // properties: [
                    //     new OA\Property(property: 'name')
                    // ]
                )
            ],
            // type: 'object',
            // properties: [
            //     new OA\Property(property: 'foo', type: 'string'),
            //     new OA\Property(property: 'bar', type: 'integer'),
            //     new OA\Property(
            //         property: 'baz',
            //         type: 'array',
            //         // items: new OA\Items(ref: new Model(type: UserIdentity::class, groups: ['full']))
            //         // items: QA\Model(\App\Entity\User::class)
            //         items: \App\Entity\User::class
            //     ),
            // ]
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
    // #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to access this page')]
    public function getProduct(\App\Entity\Product $product): JsonResponse
    {
        // throw new NotFoundHttpException('test method');
        return $this->json($product);
    }
}
