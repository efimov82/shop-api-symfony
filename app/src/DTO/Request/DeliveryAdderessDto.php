<?php

namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DeliveryAdderessDto
{
  public function __construct(

    #[Assert\NotBlank([], "Sity can't be blank")]
    public readonly string $sity,

    #[Assert\NotBlank([], "Street can't be blank")]
    public readonly string $street,

    #[Assert\NotBlank([], "House can't be blank")]
    public readonly string $house,

    public readonly string $room = '',
  ) {
  }
}
