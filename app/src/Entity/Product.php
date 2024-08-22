<?php

namespace App\Entity;

use App\Enums\SerializeGroup;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
// use OpenApi\Annotations as OA;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        SerializeGroup::MAIN->value,
        SerializeGroup::FULL->value
    ])]
    private ?string $image = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
