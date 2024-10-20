<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Entity\Fruit;
use App\Entity\Vegetable;

interface MapperInterface
{
    public function mapToEntity(object $dto): Fruit|Vegetable;
}
