<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use OpenApi\Attributes as OA;

#[Route('/api/v1/orders', name: 'orders_api')]
#[OA\Tag(name: 'Orders API')]
class OrdersController extends AbstractController
{
    #[Route('/', name: '_list')]
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
            items: new OA\Items(ref: new Model(type: Order::class)),
            example: [
                new OA\Items(new Model(type: Order::class))
            ],
        )
    )]
    public function index(OrderRepository $orderRepository, Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $orders = $orderRepository->getPaginatedOrders($page, $limit);
        $total = count($orderRepository->findAll());

        return $this->json(
            $orders,
            Response::HTTP_OK,
            [
                "x-total-items" => $total
            ]
        );
    }

    #[Route('/{id}', name: '_details')]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Get order by ID.',
        content: new Model(type: Order::class)
    )]
    // #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to access this page')]
    public function getOrder(int $id, OrderRepository $orderRepository): Response //JsonResponse
    // public function getOrder(\App\Entity\Order $order): Response
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
        $order = $orderRepository->getByIdJoinedToItems($id);
        $jsonContent = $serializer->serialize($order, 'json', ['groups' => ['main']]);
        
        return new Response($jsonContent, 200, ['Content-Type'=> 'application/json']);
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
}
