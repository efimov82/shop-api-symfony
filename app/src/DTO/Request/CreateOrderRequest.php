<?php
namespace App\DTO\Request;

use Symfony\Component\Validator\Constraints as Assert;


// #[OA\Schema('CreateOrderRequest', '#/components/schemas/CreateOrderRequest')]
// #[Model(shema: '#/components/schemas/CreateOrderRequest')]
// #[ORM\Entity(type: CreateOrderRequest::class)]
// #[Model(shema: '#/components/schemas/Create')]
class CreateOrderRequest
{
  public function __construct(

    #[Assert\NotBlank([], "date can't be blank")]
    public readonly \DateTime $delivery_date,

    // #[Assert\NotBlank]
    /**
     * @var OrderItemDto[]
     **/
    #[Assert\Type('array')]
    public readonly array $items,

    #[Assert\NotBlank([], "Comment can't be blank")]
    #[Assert\Type('string', 'Comment value should be of type string.')]
    public readonly ?string $comment
  ) {
  }

}
