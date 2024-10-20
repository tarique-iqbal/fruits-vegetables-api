<?php

namespace App\Tests\Service;

use App\Entity\Fruit;
use App\Service\FruitService;
use App\Service\FruitServiceInterface;
use App\Tests\DataFixtures\FruitFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FruitServiceTest extends KernelTestCase
{
    private FruitServiceInterface $fruitService;

    public function setUp(): void
    {
        $container = static::getContainer();
        $container->get(DatabaseToolCollection::class)
            ->get()
            ->loadFixtures([FruitFixtures::class]);

        $this->fruitService = $container->get(FruitService::class);
    }

    public function testGetPaginatedFruits(): void
    {
        $result = $this->fruitService->getPaginatedFruits(1);

        $this->assertContainsOnlyInstancesOf(Fruit::class, $result['fruits']);
        $this->assertInstanceOf(\stdClass::class, $result['pager']);
    }

    public function testGetPaginatedFruitsInvalidPage(): void
    {
        $this->expectException(BadRequestHttpException::class);

        $this->fruitService->getPaginatedFruits(99);
    }
}
