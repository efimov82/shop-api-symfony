<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

use App\Enums\SerializeGroup;
use App\Repository\DeliveryAddressRepository;


#[ORM\Entity(repositoryClass: DeliveryAddressRepository::class)]
#[ORM\Table(name: 'delivery_addresses')]
class DeliveryAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?string $sity = null;

    #[ORM\Column(length: 100)]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?string $street = null;

    #[ORM\Column(length: 50)]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?string $house = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?string $room = null;

    #[ORM\ManyToOne(inversedBy: 'DeliveryAddress')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, CustomerOrder>
     */
    #[ORM\OneToMany(targetEntity: CustomerOrder::class, mappedBy: 'deliveryAddress')]
    private Collection $CustomerOrder;

    public function __construct()
    {
        $this->CustomerOrder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSity(): ?string
    {
        return $this->sity;
    }

    public function setSity(string $sity): static
    {
        $this->sity = $sity;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): static
    {
        $this->street = $street;

        return $this;
    }

    public function getHouse(): ?string
    {
        return $this->house;
    }

    public function setHouse(string $house): static
    {
        $this->house = $house;

        return $this;
    }

    public function getRoom(): ?string
    {
        return $this->room;
    }

    public function setRoom(?string $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, CustomerOrder>
     */
    public function getCustomerOrder(): Collection
    {
        return $this->CustomerOrder;
    }

    public function addCustomerOrder(CustomerOrder $customerOrder): static
    {
        if (!$this->CustomerOrder->contains($customerOrder)) {
            $this->CustomerOrder->add($customerOrder);
            $customerOrder->setDeliveryAddress($this);
        }

        return $this;
    }

    public function removeCustomerOrder(CustomerOrder $customerOrder): static
    {
        if ($this->CustomerOrder->removeElement($customerOrder)) {
            // set the owning side to null (unless already changed)
            if ($customerOrder->getDeliveryAddress() === $this) {
                $customerOrder->setDeliveryAddress(null);
            }
        }

        return $this;
    }
}
