<?php

namespace App\Repository;

use App\Entity\CustomerOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<CustomerOrder>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerOrder::class);
    }

    /**
     * 
     * 
     * @param int $page
     * @param int $ordersPerPage
     * @param array $filters Support keys ['user_id'=> int]
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getPaginatedOrders(int $page = 1, int $ordersPerPage = 10, array $filters = []): Paginator
    {
        // $query = $this->createQueryBuilder('o')
        //     ->join('App\Entity\User', 'u');

        // if (isset($filters['user_id'])) {
        //     $query->andWhere('u.id = :user_id')
        //         ->setParameter('user_id', $filters['user_id']);
        // }
        // $query->orderBy('o.id', 'ASC')->getQuery();
        $entityManager = $this->getEntityManager();

        if (isset($filters['user_id'])) {
            $query = $entityManager->createQuery(
                'SELECT o, u
                FROM App\Entity\CustomerOrder o
                INNER JOIN o.User u
                WHERE u.id = :user_id'
            )->setParameter(
                    'user_id',
                    $filters['user_id']
                );
        } else {
            $query = $entityManager->createQuery(
                'SELECT o, u
                FROM App\Entity\CustomerOrder o
                INNER JOIN o.User u'
            );
        }

        $paginator = new Paginator($query);

        $paginator->getQuery()
            ->setFirstResult($ordersPerPage * ($page - 1))
            ->setMaxResults($ordersPerPage);

        return $paginator;
    }

    public function getByIdJoinedToItems(int $id): CustomerOrder
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT o, i
            FROM App\Entity\CustomerOrder o
            INNER JOIN o.OrderItems i
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
