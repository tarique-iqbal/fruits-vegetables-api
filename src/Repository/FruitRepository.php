<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Fruit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fruit>
 */
class FruitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fruit::class);
    }

    public function add(Fruit $fruit): void
    {
        $this->getEntityManager()->persist($fruit);
        $this->getEntityManager()->flush();
    }

    public function remove(Fruit $fruit): void
    {
        $this->getEntityManager()->remove($fruit);
        $this->getEntityManager()->flush();
    }

    public function getQuery(): Query
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.name', 'ASC')
            ->getQuery();
    }

    public function getPaginatedResultFromQuery(Query $query, int $offset, int $limit)
    {
        return $query->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();
    }
}
