<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

use OpenApi\Attributes as OA;

use App\Entity\User;
use App\Enums\SerializeGroup;
use App\Services\UserService;


#[Route('/api/v1/user', name: 'users_api')]
#[OA\Tag(name: 'Users API')]
class UserController extends AbstractRestApiController
{

  public function __construct(
    private UserService $userService,
    private SerializerInterface $serializer,
  ) {
  }

  #[Route('/list', name: '_list', methods: ['GET'])]
  public function index(): Response
  {
    $users = $this->userService->findAll();
    return $this->convertToJsonResponse($users, $this->serializer);
  }

  #[Route('/profile', name: '_profile', methods: ['GET'])]
  public function profile(TokenStorageInterface $tokenStorage): Response
  {
    $token = $tokenStorage->getToken();

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
