<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

use App\DTO\Request\OrderItemDto;
use App\DTO\Request\CreateOrderRequest;

use App\Entity\DeliveryAddress;
use App\Entity\CustomerOrder;
use App\Entity\OrderItem;
use App\Entity\User;

use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use App\Repository\ProductRepository;

use App\Exception\EntityNotFoundException;

use App\Enums\OrderStatus;

class OrderService
{
  public function __construct(
    private OrderRepository $orderRepository,
    private ProductRepository $productRepository,
    private OrderItemRepository $orderItemRepository,
    private EntityManagerInterface $entityManager,
    private DeliveryAddressService $deliveryAddressService,
  ) {
  }

  public function getOrders(User $user, int $page, int $limit): Paginator
  {
    $filter = [];

    // TODO check for manager role too
    if (!$user->hasAdminRole()) {
      $filter['user_id'] = $user->getId();
    }

    return $this->orderRepository->getPaginatedOrders($page, $limit, $filter);
  }

  public function create(CreateOrderRequest $data, User $user): CustomerOrder|\Exception
  {
    $order = new CustomerOrder();

    $order->setUser($user);
    $order->setDateCreated(new \DateTime())
      ->setComment($data->comment)
      ->setStatus(OrderStatus::NEW ->value);

    $items = $this->createOrderItems($data->items);
    $order->setOrderItems($items);

    $deliveryAddress = $this->getDeliveryAddress($data, $user);
    if ($deliveryAddress) {
      $order->setDeliveryAddress($deliveryAddress);
    }

    $this->entityManager->persist($order);
    $this->entityManager->flush();


    return $order;
  }

  /**
   * Create OrderItems array
   * 
   * @param \App\DTO\Request\OrderItemDto $data
   * @return OrderItem[] | \Exception
   */
  protected function createOrderItems(array $data): array|\Exception
  {
    $res = [];

    foreach ($data as $itemData) {
      $newOrderItem = $this->createOrderItem($itemData);
      $productIdNewItem = $newOrderItem->getProduct()->getId();

      if (isset($res[$productIdNewItem])) {
        $existItem = $res[$productIdNewItem];
        $existItem->setCount($existItem->getCount() + $newOrderItem->getCount());
      } else {
        $res[$productIdNewItem] = $newOrderItem;
      }
    }

    if (!count($res)) {
      throw new \Exception("Items count can't be empty");
    }

    return $res;
  }

  protected function createOrderItem(OrderItemDto $item): OrderItem
  {
    $product = $this->productRepository->findOneBy(['id' => $item->product_id]);

    if (!$product) {
      throw new EntityNotFoundException(\sprintf("Product not found: id=%d", $item->product_id));
    }

    if ($item->count < 1) {
      throw new \ErrorException(\sprintf("Count for product '%d' invalid", $item->product_id));
    }

    $orderItem = new OrderItem();
    $orderItem->setProduct($product)
      ->setCount($item->count);

    return $orderItem;
  }

  protected function getDeliveryAddress(CreateOrderRequest $data, User $user): DeliveryAddress|null|\Exception
  {
    
    if ($data->delivery_address_id) {
      $address = $this->deliveryAddressService->findById($data->delivery_address_id, $user);

      if (!$address) {
        throw new EntityNotFoundException(\sprintf("Delivery address id=%d not found", $data->delivery_address_id));
      }

      return $address;
    } elseif ($data->delivery_address) {
      $address = $this->deliveryAddressService->create($data->delivery_address, $user);

      return $address;
    } else {
      return null;
    }
  }

  public function delete(CustomerOrder $order): void
  {
    $this->entityManager->remove($order);
    $this->entityManager->flush();
  }
}
