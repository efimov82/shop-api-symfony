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
    private ?int $count = null;

    private int $totalCost;


    public function getTotalCost(): float {
        return 0;
        // return $this->product->getPrice() * $this->count;;
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

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }
}
