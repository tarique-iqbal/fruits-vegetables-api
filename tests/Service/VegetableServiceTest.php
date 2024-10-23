<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Vegetable;
use App\Helper\Pager;
use App\Service\VegetableService;
use App\Service\VegetableServiceInterface;
use App\Tests\DataFixtures\VegetableFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VegetableServiceTest extends KernelTestCase
{
    private VegetableServiceInterface $vegetableService;

    public function setUp(): void
    {
        $container = static::getContainer();
        $container->get(DatabaseToolCollection::class)
            ->get()
            ->loadFixtures([VegetableFixtures::class]);

        $this->vegetableService = $container->get(VegetableService::class);
    }

    public function testGetPaginatedVegetables(): void
    {
        $result = $this->vegetableService->getPaginatedVegetables(1);

        $this->assertContainsOnlyInstancesOf(Vegetable::class, $result['vegetables']);
        $this->assertInstanceOf(Pager::class, $result['pager']);
    }

    public function testGetPaginatedVegetablesPageNotFound(): void
    {
        $this->expectException(NotFoundHttpException::class);

        $this->vegetableService->getPaginatedVegetables(99);
    }
}
