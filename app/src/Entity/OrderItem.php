<?php

namespace App\Entity;

use App\Enums\SerializeGroup;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        SerializeGroup::FULL->value
    ])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?int $count = null;

    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private int $totalCost;

    #[ORM\ManyToOne(inversedBy: 'OrderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CustomerOrder $customerOrder = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?Product $Product = null;


    public function getTotalCost(): float
    {
        return $this->Product->getPrice() * $this->count;
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

    public function getCustomerOrder(): ?CustomerOrder
    {
        return $this->customerOrder;
    }

    public function setCustomerOrder(?CustomerOrder $customerOrder): static
    {
        $this->customerOrder = $customerOrder;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->Product;
    }

    public function setProduct(?Product $Product): static
    {
        $this->Product = $Product;

        return $this;
    }
}
