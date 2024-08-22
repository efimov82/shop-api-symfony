<?php

namespace App\Services;

use App\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

use App\DTO\Request\OrderItemDto;
use App\DTO\Request\CreateOrderRequest;

use App\Entity\OrderItem;
use App\Entity\Order;
use App\Entity\User;

use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use App\Repository\ProductRepository;

use App\Enums\OrderStatus;

class OrderService
{
  public function __construct(
    private OrderRepository $orderRepository,
    private ProductRepository $productRepository,
    private OrderItemRepository $orderItemRepository,
    private EntityManagerInterface $entityManager,
  ) {
  }

  public function create(CreateOrderRequest $data, User $user): Order|\Exception
  {
    $order = new Order(); // $this->orderRepository->create($data, $user);

    $order->setUserId($user->getId());
    $order->setDateCreated(new \DateTime())
      ->setStatus(OrderStatus::NEW ->value);


    $this->entityManager->persist($order);
    $this->entityManager->flush();

    // var_dump($order);
    // die();

    $items = $this->createOrderItems($data->items);

    // var_dump($items);
    // die();

    $order->setOrderItems($items);

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

      var_dump($productIdNewItem);
      die();

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

    var_dump($product);
    var_dump($item);
    die();

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
}
