<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

use App\DTO\Request\DeliveryAdderessDto;
use App\Entity\DeliveryAddress;
use App\Entity\User;
use App\Repository\DeliveryAddressRepository;

class DeliveryAddressService
{
  public function __construct(
    private EntityManagerInterface $entityManager,
    private DeliveryAddressRepository $deliveryAddressRepository,
  ) {
  }

  public function findById($id, User $user): DeliveryAddress|null
  {
    return $this->deliveryAddressRepository->findOneBy(['id'=>$id, 'user'=>$user]);
  }

  public function create(DeliveryAdderessDto $data, User $user): DeliveryAddress|\Exception
  {
    $address = new DeliveryAddress();

    $address->setSity($data->sity)
      ->setStreet($data->street)
      ->setHouse($data->house)
      ->setRoom($data->room)
      ->setUser($user);

    $this->entityManager->persist($address);
    $this->entityManager->flush();

    return $address;
  }
}
