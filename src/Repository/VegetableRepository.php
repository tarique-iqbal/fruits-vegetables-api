<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Vegetable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vegetable>
 */
class VegetableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vegetable::class);
    }

    public function getQuery(): Query
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.name', 'ASC')
            ->getQuery();
    }

    public function getPaginatedResultFromQuery(Query $query, int $offset, int $limit)
    {
        return $query->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();
    }
}
