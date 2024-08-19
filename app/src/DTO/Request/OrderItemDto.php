<?php
namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;

class OrderItemDto
{
  public function __construct(
    #[Assert\NotBlank([], "product_id can't be blank")]
    public readonly int $product_id,

    #[Assert\NotBlank([], "count can't be blank")]
    public readonly int $count,

  ) {
  }
}
