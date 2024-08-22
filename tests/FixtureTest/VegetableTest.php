<?php

declare(strict_types=1);

namespace App\Tests\FixtureTest;

use App\Entity\Vegetable;

class VegetableTest extends DataFixtureTestCase
{
    public function testFruitList(): void
    {
        $fruits = $this->entityManager->getRepository(Vegetable::class)->findAll();

        $this->assertContainsOnlyInstancesOf(Vegetable::class, $fruits);
    }
}
