<?php

namespace App\Entity;

use App\Repository\OrderRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class CustomerOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['main'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    // #[Context([DateTimeNormalizer::FORMAT_KEY => \DateTime::RFC3339])]
    // #[Context(
    //     context: [DateTimeNormalizer::FORMAT_KEY => \DateTime::RFC3339_EXTENDED],
    //     groups: ['main'],
    // )]
    private ?\DateTimeInterface $date_created = null;

    #[ORM\Column]
    #[Groups(['main'])]
    private ?int $status = null;

    #[ORM\Column]
    #[Groups(['main'])]
    private ?string $comment = '';

    public function __construct()
    {

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

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->date_created;
    }

    public function setDateCreated(\DateTimeInterface $date_created): static
    {
        $this->date_created = $date_created;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }


    // /**
    //  * @param array $orderItems
    //  */
    // public function setOrderItems(array $orderItems): static
    // {
    //     foreach ($orderItems as $orderItem) {
    //         $this->addOrderItem($orderItem);
    //     }

    //     return $this;
    // }

    // public function addOrderItem(OrderItem $orderItem): static
    // {
    //     if (!$this->orderItems->contains($orderItem)) {
    //         $this->orderItems->add($orderItem);
    //         $orderItem->setOrderEntity($this);
    //     }

    //     return $this;
    // }

    // public function removeOrderItem(OrderItem $orderItem): static
    // {
    //     if ($this->orderItems->removeElement($orderItem)) {
    //         // set the owning side to null (unless already changed)
    //         if ($orderItem->getOrderEntity() === $this) {
    //             $orderItem->setOrderEntity(null);
    //         }
    //     }

    //     return $this;
    // }
}