<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Get total revenue from all paid and pending orders (for testing)
     */
    public function getTotalRevenue(): float
    {
        $result = $this->createQueryBuilder('o')
            ->select('SUM(o.totalAmount) as total')
            ->where('o.status IN (:statuses)')
            ->setParameter('statuses', ['paid', 'pending'])
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /**
     * Get revenue grouped by date for the last N days
     * Includes both paid and pending orders for testing
     */
    public function getRevenueByDate(int $days = 30): array
    {
        $startDate = new \DateTimeImmutable("-{$days} days");
        $results = $this->createQueryBuilder('o')
            ->select('o.createdAt as createdAt, o.totalAmount as totalAmount')
            ->where('o.createdAt >= :startDate')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('startDate', $startDate)
            ->setParameter('statuses', ['paid', 'pending'])
            ->orderBy('o.createdAt', 'ASC')
            ->getQuery()
            ->getArrayResult();

        // Aggregate by date in PHP (YYYY-MM-DD)
        $map = [];
        foreach ($results as $row) {
            $dt = $row['createdAt'];
            $dateKey = $dt instanceof \DateTimeInterface ? $dt->format('Y-m-d') : (new \DateTime($dt))->format('Y-m-d');
            $map[$dateKey] = ($map[$dateKey] ?? 0) + (float) $row['totalAmount'];
        }

        // Ensure continuous range for charting
        $data = [];
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), new \DateTimeImmutable("+1 day"));
        foreach ($period as $p) {
            $d = $p->format('Y-m-d');
            $data[] = [
                'date' => $d,
                'revenue' => (float) ($map[$d] ?? 0),
            ];
        }

        return $data;
    }

    /**
     * Get orders count grouped by date for the last N days
     * Includes both paid and pending orders for testing
     */
    public function getOrdersByDate(int $days = 30): array
    {
        $startDate = new \DateTimeImmutable("-{$days} days");
        $results = $this->createQueryBuilder('o')
            ->select('o.createdAt as createdAt, o.id as id')
            ->where('o.createdAt >= :startDate')
            ->andWhere('o.status IN (:statuses)')
            ->setParameter('startDate', $startDate)
            ->setParameter('statuses', ['paid', 'pending'])
            ->orderBy('o.createdAt', 'ASC')
            ->getQuery()
            ->getArrayResult();

        // Count orders per date in PHP
        $map = [];
        foreach ($results as $row) {
            $dt = $row['createdAt'];
            $dateKey = $dt instanceof \DateTimeInterface ? $dt->format('Y-m-d') : (new \DateTime($dt))->format('Y-m-d');
            $map[$dateKey] = ($map[$dateKey] ?? 0) + 1;
        }

        // Ensure continuous range for charting
        $data = [];
        $period = new \DatePeriod($startDate, new \DateInterval('P1D'), new \DateTimeImmutable("+1 day"));
        foreach ($period as $p) {
            $d = $p->format('Y-m-d');
            $data[] = [
                'date' => $d,
                'count' => (int) ($map[$d] ?? 0),
            ];
        }

        return $data;
    }

    /**
     * Get order status breakdown
     */
    public function getStatusBreakdown(): array
    {
        $results = $this->createQueryBuilder('o')
            ->select('o.status, COUNT(o.id) as count')
            ->groupBy('o.status')
            ->getQuery()
            ->getResult();

        $breakdown = [];
        foreach ($results as $row) {
            $breakdown[$row['status']] = (int) $row['count'];
        }

        return $breakdown;
    }
}
