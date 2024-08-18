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

    #[ORM\Column]
    #[Groups(['main'])]
    private ?int $order_id = null;

    #[ORM\Column]
    #[Groups(['main'])]
    private ?int $product_id = null;

    #[ORM\Column]
    #[Groups(['main'])]
    private ?int $count = null;

    #[ORM\ManyToOne(inversedBy: 'OrderItems')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['extended'])]
    private ?Order $OrderObj = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getOrderId(): ?int
    {
        return $this->order_id;
    }

    public function setOrderId(int $order_id): static
    {
        $this->order_id = $order_id;

        return $this;
    }

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

    public function getOrder(): ?Order
    {
        return $this->OrderObj;
    }

    public function setOrder(?Order $order): static
    {
        $this->OrderObj = $order;

        return $this;
    }
}
