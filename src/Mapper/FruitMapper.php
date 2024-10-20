<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\Fruit;

class FruitMapper implements MapperInterface
{
    public function mapToEntity(object $dto): Fruit
    {
        $gram = $dto->getUnit() === 'kg' ?
            $dto->getQuantity() * 1000 :
            $dto->getQuantity();

        $fruit = new Fruit();
        $fruit->setName($dto->getName())
            ->setAlias($dto->getAlias())
            ->setGram($gram);

        return $fruit;
    }
}
