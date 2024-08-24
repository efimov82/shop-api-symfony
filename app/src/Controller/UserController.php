<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

use OpenApi\Attributes as OA;

use App\Entity\User;
use App\Enums\SerializeGroup;
use App\DTO\Request\DeliveryAdderessDto;
use App\Services\UserService;
use App\Services\DeliveryAddressService;


#[Route('/api/v1/user', name: 'users_api')]
#[OA\Tag(name: 'Users API')]
class UserController extends AbstractRestApiController
{

  public function __construct(
    private UserService $userService,
    private DeliveryAddressService $deliveryAddressService,
    private SerializerInterface $serializer,
    private TokenStorageInterface $tokenStorage,
  ) {
  }

  #[Route('/list', name: '_list', methods: ['GET'])]
  public function index(): Response
  {
    $users = $this->userService->findAll();
    return $this->convertToJsonResponse($users, $this->serializer);
  }

  #[Route('/profile', name: '_profile', methods: ['GET'])]
  public function profile(): Response
  {
    $token = $this->tokenStorage->getToken();

    try {
      $user = $this->userService->getUserByToken($token);
    } catch (\Exception $e) {
      return $this->json(
        [
          'error' => 'Profile not found'
        ],
        Response::HTTP_BAD_REQUEST
      );
    }

    return $this->convertToJsonResponse($user, $this->serializer, [SerializeGroup::FULL->value]);
  }

  #[OA\Response(
    response: Response::HTTP_OK,
    description: 'Get user addresses.',

    content: new OA\JsonContent(
      type: 'array',
      items: new OA\Items(ref: '#/components/schemas/DeliveryAddress')
    )
  )]
  #[Route('/addesses', name: '_addesses', methods: ['GET'])]

  public function addesses(): Response
  {
    $token = $this->tokenStorage->getToken();

    try {
      $user = $this->userService->getUserByToken($token);
    } catch (\Exception $e) {
      return $this->json(
        [
          'error' => 'Profile not found'
        ],
        Response::HTTP_BAD_REQUEST
      );
    }

    return $this->convertToJsonResponse($user->getDeliveryAddress(), $this->serializer, [SerializeGroup::MAIN->value]);
  }

  #[Route('/addesses', name: '_add', methods: ['POST'])]
  #[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
      type: 'object',
      ref: '#/components/schemas/DeliveryAdderessDto',
    )
  )]
  public function addAddess(#[MapRequestPayload(acceptFormat: 'json')] DeliveryAdderessDto $data): Response
  {
    $token = $this->tokenStorage->getToken();

    try {
      $user = $this->userService->getUserByToken($token);
    } catch (\Exception $e) {
      return $this->json(
        [
          'error' => 'Profile not found'
        ],
        Response::HTTP_BAD_REQUEST
      );
    }

    $address = $this->deliveryAddressService->create($data, $user);

    return $this->convertToJsonResponse($address, $this->serializer, [SerializeGroup::MAIN->value]);
  }

  #[Route('/details/{id}', name: '_delails', methods: ['GET'])]
  public function details(User $user): Response
  {
    return $this->convertToJsonResponse($user, $this->serializer, [SerializeGroup::FULL->value]);
  }


  public function delete(User $user): Response
  {
    return new Response('NOT_IMPLEMENTE', Response::HTTP_NOT_IMPLEMENTED);
  }
}
