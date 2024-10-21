<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\Vegetable;

class VegetableMapper implements MapperInterface
{
    public function mapToEntity(object $dto): Vegetable
    {
        $gram = $dto->getUnit() === 'kg' ?
            $dto->getQuantity() * 1000 :
            $dto->getQuantity();

        $vegetable = new Vegetable();
        $vegetable->setName($dto->getName())
            ->setAlias($dto->getAlias())
            ->setGram($gram);

        return $vegetable;
    }
}
