<?php

namespace App\Entity;

use App\Entity\Order;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['main'])]
    private ?int $id = null;

    // #[ORM\Column]
    // #[Groups(['main'])]
    // private ?int $order_id = null;

    #[ORM\Column]
    #[Groups(['main'])]
    private ?int $product_id = null;

    #[ORM\Column]
    #[Groups(['main'])]
    private ?int $count = null;

    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $OrderEntity = null;

    // #[ORM\OneToOne(cascade: ['persist', 'remove'])]

// Все переделать удалить и создать связи как в Order OneToMany 
    // #[ORM\ManyToOne(inversedBy: 'orderItems')]
    // #[ORM\JoinColumn(nullable: false)]
    // private ?Product $Product = null;

    // #[ORM\ManyToOne(inversedBy: 'OrderItems')]
    // #[ORM\JoinColumn(nullable: false)]
    // #[Groups(['extended'])]
    // private ?Order $OrderObj = null;
    private int $totalCost;

    #[ORM\ManyToOne(inversedBy: 'orderProductItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    public function getTotalCost(): float {
        return $this->product->getPrice() * $this->count;;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    // public function getOrderId(): ?int
    // {
    //     return $this->order_id;
    // }

    // public function setOrderId(int $order_id): static
    // {
    //     $this->order_id = $order_id;

    //     return $this;
    // }

    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    public function setProductId(int $product_id): static
    {
        $this->product_id = $product_id;

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    // public function getOrder(): ?Order
    // {
    //     return $this->OrderObj;
    // }

    // public function setOrder(?Order $order): static
    // {
    //     $this->OrderObj = $order;

    //     return $this;
    // }

    public function getOrderEntity(): ?Order
    {
        return $this->OrderEntity;
    }

    public function setOrderEntity(?Order $OrderEntity): static
    {
        $this->OrderEntity = $OrderEntity;

        return $this;
    }

    // public function getProduct(): ?Product
    // {
    //     return $this->Product;
    // }

    // public function setProduct(Product $Product): static
    // {
    //     $this->Product = $Product;

    //     return $this;
    // }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
