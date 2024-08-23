<?php

namespace App\Services;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\Token\JWTPostAuthenticationToken;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Enums\Roles;

class UserService
{
  public function __construct(
    private EntityManagerInterface $entityManager,
    private UserRepository $userRepository,
    private UserPasswordHasherInterface $passwordHasher
  ) {
  }

  /**
   * Get list all users 
   * 
   * @return User[]
   */
  public function findAll(): array
  {
    return $this->userRepository->findAll();
  }

  public function create(object $data, Roles $role): User
  {
    $user = new User();

    // hash the password (based on the security.yaml config for the $user class)
    $hashedPassword = $this->passwordHasher->hashPassword(
      $user,
      $data->password
    );

    $user->setFirstName($data->first_name)
      ->setLastName($data->last_name)
      ->setEmail($data->email)
      ->setPassword($hashedPassword)
      ->setRoles([$role]);

    $this->entityManager->persist($user);
    $this->entityManager->flush();

    return $user;
  }

  public function getUserByEmail(string $email): User
  {
    return $this->userRepository->findOneBy(["email" => $email]);
  }

  public function getUserByToken(TokenInterface|null $token): User|\Exception
  {
    if ($token instanceof JWTPostAuthenticationToken) {
      $userFromToken = $token->getUser();

      $user = $this->userRepository->findOneBy(["email" => $userFromToken->getUserIdentifier()]);

      return $user;
    } else {
      throw new \Exception("Invalid token");
    }
  }
}
