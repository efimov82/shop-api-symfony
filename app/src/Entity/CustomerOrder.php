<?php

namespace App\Entity;

use App\Enums\SerializeGroup;
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
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    // #[Context([DateTimeNormalizer::FORMAT_KEY => \DateTime::RFC3339])]
    // #[Context(
    //     context: [DateTimeNormalizer::FORMAT_KEY => \DateTime::RFC3339_EXTENDED],
    //     groups: ['main'],
    // )]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?\DateTimeInterface $date_created = null;

    #[ORM\Column]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?int $status = null;

    #[ORM\Column]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?string $comment = '';

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'customerOrder', cascade: ["persist", "remove"])]
    #[Groups([
        SerializeGroup::FULL->value
    ])]
    private Collection $OrderItems;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?User $User = null;

    public function __construct()
    {
        $this->OrderItems = new ArrayCollection();
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }


    /**
     * @param array $orderItems
     */
    public function setOrderItems(array $orderItems): static
    {
        foreach ($orderItems as $orderItem) {
            $this->addOrderItem($orderItem);
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->OrderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->OrderItems->contains($orderItem)) {
            $this->OrderItems->add($orderItem);
            $orderItem->setCustomerOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->OrderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getCustomerOrder() === $this) {
                $orderItem->setCustomerOrder(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }
}
