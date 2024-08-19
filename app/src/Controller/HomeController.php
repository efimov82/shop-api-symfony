<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[Route('/api/v1', name: 'home_api')]
#[OA\Tag(name: 'Home API')]
class HomeController extends AbstractController
{
  #[Route('/home', name: '_index')]
  // #[IsGranted('ROLE_USER', statusCode: 423, message: 'You are not allowed to view this page')]
  public function home(TokenStorageInterface $tokenStorage): JsonResponse
  {
    $token = $tokenStorage->getToken();

    if ($token instanceof JWTPostAuthenticationToken) 
    {
      $user = $token->getUser();
      $roles = $user->getRoles();

      return $this->json([
        'message' => sprintf('Welcome %s!', $user->getEmail()),
        'id' => $user->getId(),
        'roles' => $roles,
      ]);
    } else {
      return $this->json([
        'message' => 'Token not provided'
      ]);
    }
  }
}
