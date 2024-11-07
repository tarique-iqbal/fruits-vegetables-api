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

    public function add(Vegetable $vegetable): void
    {
        $this->getEntityManager()->persist($vegetable);
        $this->getEntityManager()->flush();
    }

    public function remove(Vegetable $vegetable): void
    {
        $this->getEntityManager()->remove($vegetable);
        $this->getEntityManager()->flush();
    }

    public function existsByAlias(string $alias): bool
    {
        return $this->findOneBy(['alias' => $alias]) instanceof Vegetable;
    }

    public function getQuery(): Query
    {
        return $this->createQueryBuilder('v')
            ->orderBy('v.name', 'ASC')
            ->getQuery();
    }

    public function getPaginatedResult(Query $query, int $offset, int $limit)
    {
        return $query->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();
    }
}
