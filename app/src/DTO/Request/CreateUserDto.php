<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CreateUserDto
{
  public function __construct(
    #[Assert\NotBlank([], "First name can't be blank")]
    #[SerializedName('first_name')]
    public readonly string $first_name,

    #[Assert\NotBlank([], "Last name can't be blank")]
    #[SerializedName('last_name')]
    public readonly string $last_name,

    // #[Assert\Email]
    public readonly string $email,

    #[Assert\NotBlank([], "Password can't be blank")] // TODO Add password validation rules
    public readonly string $password,
  ) {
  }
}