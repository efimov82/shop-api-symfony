<?php

namespace App\Controller;

use App\Enums\SerializeGroup;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Nelmio\ApiDocBundle\Annotation\Model;

use OpenApi\Attributes as OA;

use App\DTO\Request\CreateOrderRequest;
use App\Entity\CustomerOrder;
use App\Repository\OrderRepository;
use App\Services\OrderService;
use App\Services\UserService;


#[Route('/api/v1/user/orders', name: 'orders_api')]
#[OA\Tag(name: 'Orders API')]
class OrdersController extends AbstractRestApiController
{
    public function __construct(
        private UserService $userService,
        private OrderService $orderService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    #[Route('', name: '_list', methods: ['GET'])]
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
            items: new OA\Items(ref: '#/components/schemas/CustomerOrder'),
            example: [
                new OA\Schema(ref: '#/components/schemas/CustomerOrder'),
                new OA\Items(ref: '#/components/schemas/CustomerOrder'),
                new Model(type: CustomerOrder::class),
            ],
        )
    )]
    #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to access this page')]
    public function index(OrderRepository $orderRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $token = $this->tokenStorage->getToken();

        try {
            $user = $this->userService->getUserByToken($token);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Wrong token'
            ], Response::HTTP_BAD_REQUEST);
        }

        $orders = $this->orderService->getOrders($user, $page, $limit);
        $total = count($orderRepository->findAll()); // TODO add user filtering here

        $additoinalHeaders = ["x-total-items" => $total];

        return $this->convertToJsonResponse(
            $orders,
            $this->serializer,
            [SerializeGroup::MAIN->value],
            Response::HTTP_OK,
            $additoinalHeaders
        );
    }

    #[Route('/{id}', name: '_details', methods: ['GET'])]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Get order by ID.',
        content: new Model(type: CustomerOrder::class)
    )]
    #[IsGranted(ACTION_VIEW_ORDER, 'order', statusCode: 423, message: 'You are not allowed to access this page')]
    public function getOrder(CustomerOrder $order): Response
    {
        // TODO check voter + add tests
        return $this->convertToJsonResponse($order, $this->serializer, [SerializeGroup::FULL->value], );
    }

    #[Route('', name: '_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to this resource.')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            ref: '#/components/schemas/CreateOrderRequest',
        )
    )]
    public function createOrder(#[MapRequestPayload()] CreateOrderRequest $data): JsonResponse //Order
    {
        $token = $this->tokenStorage->getToken();

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
                'error' => $e->getMessage(), // TODO check do we need replace message here
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
        response: Response::HTTP_NO_CONTENT,
        description: 'Get order by ID.',
        content: new Model(type: CustomerOrder::class)
    )]
    // #[IsGranted('ROLE_ADMIN', statusCode: 423, message: 'You are not allowed to access this page')]
    public function deleteOrder(CustomerOrder $order): Response
    {
        $this->orderService->delete($order);

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
