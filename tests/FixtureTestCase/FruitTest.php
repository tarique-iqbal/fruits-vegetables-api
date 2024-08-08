<?php

declare(strict_types=1);

namespace App\Tests\FixtureTestCase;

use App\Entity\Fruit;

class FruitTest extends DataFixtureTestCase
{
    public function testFruitList(): void
    {
        $fruits = $this->entityManager->getRepository(Fruit::class)->findAll();

        $this->assertContainsOnlyInstancesOf(Fruit::class, $fruits);
    }
}
