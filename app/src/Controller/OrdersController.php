<?php

namespace App\Controller;

use App\DTO\Request\CreateOrderRequest;
use App\DTO\Request\OrderItemDto;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use Nelmio\ApiDocBundle\Annotation\Model;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use OpenApi\Attributes as OA;

#[Route('/api/v1/orders', name: 'orders_api')]
#[OA\Tag(name: 'Orders API')]
class OrdersController extends AbstractRestApiController
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/', name: '_list', methods: ['GET'])]
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
        description: 'Get orders.',

        content: new OA\JsonContent(
            type: 'array',
            // items: new OA\Items(new Model(type: Order::class)),
            items: new OA\Items(ref: '#/components/schemas/Order'),
            // items: new OA\Schema(ref: '#/components/schemas/Order'),
            example: [
                new OA\Schema(ref: '#/components/schemas/Order'),
                new OA\Items(ref: '#/components/schemas/Order'),
                new Model(type: Order::class),

                // 0 => OA\Model(Order::class),
                // 1 => new OA\Items(new Model(type: Order::class)),
            ],
        )
    )]
    public function index(OrderRepository $orderRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $orders = $orderRepository->getPaginatedOrders($page, $limit);
        $total = count($orderRepository->findAll());

        $jsonContent = $this->convertToJson($orders);

        return new Response(
            $jsonContent,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json',
                "x-total-items" => $total
            ]
        );

        //$order = $orderRepository->getByIdJoinedToItems($id);

        // return $this->json(
        //     $orders,
        //     Response::HTTP_OK,
        //     [
        //         "x-total-items" => $total
        //     ]
        // );
    }

    #[Route('/{id}', name: '_details')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Get order by ID.',
        content: new Model(type: Order::class)
    )]
    // #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to access this page')]
    // public function getOrder(int $id, OrderRepository $orderRepository): Response //JsonResponse
    public function getOrder(\App\Entity\Order $order): Response
    {
        // $normalizers = array(new ObjectNormalizer());
        // $serializer = new Serializer($normalizers, $encoders);
        $encoders = [new JsonEncoder()];

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (object $object, string $format, array $context): string {
                return $object->getId();
            },
        ];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];
        $serializer = new Serializer($normalizers, $encoders);

        // TODO check user role + owner if need
        // $order = $orderRepository->getByIdJoinedToItems($id);
        $jsonContent = $serializer->serialize($order, 'json', ['groups' => ['main']]);

        return new Response($jsonContent, 200, ['Content-Type' => 'application/json']);
        // $items = $order->getOrderItems();
        // return $this->json($order);
        //die();

        $res = [];
        $items = $order->getOrderItems();
        foreach ($items as $item) {
            // $res[] = ['id' => $item->getId(), 'product_id' => $item->getProductId()];
            $res[] = $this->json($item);
        }

        var_dump($res);
        die();

        //return $this->json($order, 200, [], ['groups' => ['main']]);
        return $res; //$this->json($res);

        var_dump($items);
        die();

        return new Response($res); // $this->json($items);
    }

    #[Route('', name: '_create', methods: ['POST'])]
    // #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to access this page')]
    #[OA\RequestBody(
        required: true,
        // content: new OA\JsonContent(
        //     type: 'object', //CreateOrderRequest::class, // CreateOrderRequest, //
        //     example: [
        //         "delivery_date" => "2024-08-19",
        //         "comment" => "Order comment",
        //         "products" => new OA\Items(ref: CreateOrderRequest::class),
        //         // "items" => new OA\Items(new Model(type: CreateOrderRequest::class))
        //         // "items" => new OQ\Items(
        //         //     type: 'array',
        //         //     items: new OA\Items(ref: new Model(type: Order::class)),   
        //         // )
        //     ]
        // )
        // content: new Model(type: CreateOrderRequest::class),

        content: new OA\JsonContent(
            type: 'object',
            ref: '#/components/schemas/CreateOrderRequest',
            // example: [
            //     "items" => new OA\Items(
            //         type: 'array',
            //         // items: new OA\Items(new Model(type: OrderItemDto::class)),   
            //         items: new OA\Items(ref: OrderItemDto::class),
            //         //schema: '#/components/schemas/CreateOrderRequest',   
            //     )
            // ]
        )
    )]
    public function createOrder(#[MapRequestPayload()] CreateOrderRequest $data, TokenStorageInterface $tokenStorage): JsonResponse //Order
    //public function createOrder(Request $request, TokenStorageInterface $tokenStorage): Order
    {
        $token = $tokenStorage->getToken();

        return $this->json([
            'response' => 'ok',
            'date' => $data->delivery_date,
            'comment' => $data->comment,
        ]);


        // $item = new OrderItem();
        // $item->setProduct();

        if ($token instanceof JWTPostAuthenticationToken) {
            $user = $token->getUser();
        }
    }
}
