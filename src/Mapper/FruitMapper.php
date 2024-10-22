<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\Response\FruitDto;
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

    public function mapAllToDto(array $entities): array
    {
        $result = [];

        foreach ($entities as $entity) {
            $fruit = new FruitDto();
            $fruit->setId($entity->getId())
                ->setName($entity->getName())
                ->setAlias($entity->getAlias())
                ->setGram($entity->getGram())
                ->setKilogram(($entity->getGram() / 1000))
                ->setCreatedAt($entity->getCreatedAt());

            $result[] = $fruit;
        }

        return $result;
    }
}
