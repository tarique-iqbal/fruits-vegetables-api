<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Vegetable;
use App\Helper\PaginationHelper;
use App\Repository\VegetableRepository;

final readonly class VegetableService implements VegetableServiceInterface
{
    public function __construct(
        private VegetableRepository $vegetableRepository
    ) {
    }

    public function addVegetable(Vegetable $vegetable): void
    {
        $this->vegetableRepository->add($vegetable);
    }

    public function removeVegetable(Vegetable $vegetable): void
    {
        $this->vegetableRepository->remove($vegetable);
    }

    public function findById(int $id): Vegetable|null
    {
        return $this->vegetableRepository->find($id);
    }

    public function getPaginatedVegetables(int $page): array
    {
        $query = $this->vegetableRepository->getQuery();
        $pager = (new PaginationHelper($query))->paginate($page);
        $vegetables = $this->vegetableRepository->getPaginatedResult($query, $pager->getOffset(), $pager->getLimit());

        return [
            'vegetables' => $vegetables,
            'pager' => $pager
        ];
    }
}
