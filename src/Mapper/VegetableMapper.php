<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\Response\VegetableDto;
use App\Entity\Vegetable;
use App\Utility\Utility;

class VegetableMapper implements MapperInterface
{
    public function mapToEntity(object $dto): Vegetable
    {
        $gram = Utility::convertToGram($dto->getUnit(), $dto->getQuantity());

        $vegetable = new Vegetable();
        $vegetable->setName($dto->getName())
            ->setAlias($dto->getAlias())
            ->setGram($gram);

        return $vegetable;
    }

    public function mapAllToDto(array $entities): array
    {
        $result = [];

        foreach ($entities as $entity) {
            $fruit = new VegetableDto();
            $fruit->setId($entity->getId())
                ->setName($entity->getName())
                ->setAlias($entity->getAlias())
                ->setGram($entity->getGram())
                ->setKilogram($entity->getGram() / 1000)
                ->setCreatedAt($entity->getCreatedAt());

            $result[] = $fruit;
        }

        return $result;
    }
}
