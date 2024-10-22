<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Fruit;
use App\Helper\PaginationHelper;
use App\Repository\FruitRepository;

final readonly class FruitService implements FruitServiceInterface
{
    public function __construct(
        private FruitRepository $fruitRepository
    ) {
    }

    public function addFruit(Fruit $fruit): void
    {
        $this->fruitRepository->add($fruit);
    }

    public function removeFruit(Fruit $fruit): void
    {
        $this->fruitRepository->remove($fruit);
    }

    public function findById(int $id): Fruit|null
    {
        return $this->fruitRepository->find($id);
    }

    public function getPaginatedFruits(int $page): array
    {
        $query = $this->fruitRepository->getQuery();
        $pager = (new PaginationHelper($query))->paginate($page);
        $fruits = $this->fruitRepository->getPaginatedResult($query, $pager->getOffset(), $pager->getLimit());

        return [
            'fruits' => $fruits,
            'pager' => $pager
        ];
    }
}
