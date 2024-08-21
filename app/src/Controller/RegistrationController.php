<?php
namespace App\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

use OpenApi\Attributes as OA;

use App\DTO\Request\CreateUserDto;
use App\Enums\Roles;
use App\Services\UserService;


#[Route('/api/v1', name: 'registration_api')]
#[OA\Tag(name: 'Auth API')]
class RegistrationController extends AbstractController
{

  public function __construct(
    private UserService $userService
  ) {
  }

  #[Route('/registration', name: '_index', methods: ['POST'])]
  #[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
      type: 'object',
      ref: '#/components/schemas/CreateUserDto',
    )
  )]
  public function index(#[MapRequestPayload(acceptFormat: 'json')] CreateUserDto $data): JsonResponse
  {
    try {
      $user = $this->userService->create($data, Roles::USER);
    } catch (UniqueConstraintViolationException $e) {
      return new JsonResponse(['error' => 'userAlreadyExist'], Response::HTTP_CONFLICT); // TODO check HTTP error code
    }

    // TODO do we need send some response here ?
    // $resp = ["email" => $user->getEmail(), "roles" => $user->getRoles()];

    return new JsonResponse(null, Response::HTTP_CREATED);
  }

  //#[Route('/login', name: '_login', methods: ['POST'])]
  // this route implemented by Lexik\Bundle\JWTAuthenticationBundle

  // #[Route('/logout', name: '_logout', methods: ['POST'])]
  // public function logout()
  // {
  //   // TODO do we need this method?
  // }
}
