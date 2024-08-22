<?php

namespace App\Controller;

use App\DTO\Request\CreateOrderRequest;
use App\Entity\CustomerOrder;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Services\OrderService;
use App\Services\UserService;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
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

#[Route('/api/v1/user/orders', name: 'orders_api')]
#[OA\Tag(name: 'Orders API')]
class OrdersController extends AbstractRestApiController
{
    public function __construct(
        private UserService $userService,
        private OrderService $orderService,
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
            items: new OA\Items(ref: '#/components/schemas/Order'),
            example: [
                new OA\Schema(ref: '#/components/schemas/Order'),
                new OA\Items(ref: '#/components/schemas/Order'),
                new Model(type: Order::class),
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
    }

    #[Route('/{id}', name: '_details', methods: ['GET'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Get order by ID.',
        content: new Model(type: Order::class)
    )]
    // #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to access this page')]
    public function getOrder(CustomerOrder $order): Response
    {
        // TODO check user role + owner if need

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
    #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to this resource.')]
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
    {
        $token = $tokenStorage->getToken();

        if ($token instanceof JWTPostAuthenticationToken) {
            $userFromToken = $token->getUser();
            $user = $this->userService->getUserByEmail($userFromToken->getUserIdentifier());
            $roles = $user->getRoles();
        } else {
            return $this->json(
                [
                    'error' => 'Token not provided or wrong'
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $order = $this->orderService->create($data, $user);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
        
        $resp = [
            'id' => $order->getId(),
            'status' => $order->getStatus()
        ];

        return new JsonResponse($resp, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: '_delete')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Get order by ID.',
        content: new Model(type: Order::class)
    )]
    // #[IsGranted('ROLE_ADMIN', statusCode: 423, message: 'You are not allowed to access this page')]
    public function deleteOrder(CustomerOrder $order): Response
    {
        $this->orderService->delete($order);

        return new Response(null, Response::HTTP_OK);
    }
}
