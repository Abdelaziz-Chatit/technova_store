<?php

namespace App\Repository;

use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItem>
 */
class OrderItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderItem::class);
    }

    /**
     * Get top N products by revenue
     */
    public function getTopProductsByRevenue(int $limit = 5): array
    {
        $results = $this->createQueryBuilder('oi')
            ->select('p.id, p.name, SUM(oi.price * oi.quantity) as revenue')
            ->join('oi.product', 'p')
            ->join('oi.order', 'o')
            ->where('o.status = :status')
            ->setParameter('status', 'paid')
            ->groupBy('p.id', 'p.name')
            ->orderBy('revenue', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $results;
    }
}
