<?php

namespace App\Repository;

use App\Entity\Advertisement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Advertisement>
 */
class AdvertisementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advertisement::class);
    }

    /**
     * Get all active advertisements ordered by order field
     */
    public function findActiveAdvertisements()
    {
        return $this->createQueryBuilder('a')
            ->where('a.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('a.order', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find advertisement by ID
     */
    public function findById(int $id): ?Advertisement
    {
        return $this->find($id);
    }
}
