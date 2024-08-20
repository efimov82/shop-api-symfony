<?php

namespace App\Services;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\User;
use App\Enums\Roles;

class UserService
{
  public function __construct(
    private EntityManagerInterface $entityManager,
    private UserPasswordHasherInterface $passwordHasher
  ) {
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
}
