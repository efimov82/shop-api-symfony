<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

  #[Route('/{id}', name: '_delails', methods: ['GET'])]
  public function details(User $user, SerializerInterface $serializer): Response
  {
    return $this->convertToJsonResponse($user, $this->serializer, [SerializeGroup::FULL->value]);
  }
}
