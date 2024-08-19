<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getPaginatedOrders(int $page = 1, int $ordersPerPage = 10): Paginator
    {
        $query = $this->createQueryBuilder('o')
            ->orderBy('o.id', 'ASC')
            ->getQuery();
        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult($ordersPerPage * ($page - 1))
            ->setMaxResults($ordersPerPage);

        return $paginator;
    }

    public function getByIdJoinedToItems(int $id): ?Order //array//
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT o, i
            FROM App\Entity\Order o
            INNER JOIN o.OrderEntity i
            WHERE o.id = :id'
        )->setParameter('id', $id);

        return $query->getOneOrNullResult();
        //return $query->getArrayResult();
    }

    //    /**
    //     * @return Order[] Returns an array of Order objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
