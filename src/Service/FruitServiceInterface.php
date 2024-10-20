<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Fruit;

interface FruitServiceInterface
{
    public function addFruit(Fruit $fruit): void;

    public function removeFruit(Fruit $fruit): void;

    public function findById(int $id): Fruit|null;

    public function getPaginatedFruits(int $page): array;
}
