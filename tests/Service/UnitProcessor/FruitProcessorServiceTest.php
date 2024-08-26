<?php

declare(strict_types=1);

namespace App\Tests\Service\UnitProcessor;

use App\Service\UnitProcessor\FruitProcessorService;
use App\Service\UnitProcessor\UnitProcessorServiceInterface;
use App\Tests\DataFixtures\FruitFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FruitProcessorServiceTest extends KernelTestCase
{
    private UnitProcessorServiceInterface $fruitProcessorService;

    public function setUp(): void
    {
        $container = static::getContainer();
        $container->get(DatabaseToolCollection::class)
            ->get()
            ->loadFixtures([FruitFixtures::class]);

        $this->fruitProcessorService = $container->get(FruitProcessorService::class);
    }

    public function testProcess(): void
    {
        $object = json_decode('{"id":18,"name":"Kiwi","type":"fruit","quantity":10,"unit":"kg"}');
        $status = $this->fruitProcessorService->process($object);

        $this->assertTrue($status);
    }

    public function testProcessFruitExistInDatabase(): void
    {
        $object = json_decode('{"id":2,"name":"Apples","type":"fruit","quantity":20,"unit":"kg"}');
        $status = $this->fruitProcessorService->process($object);

        $this->assertFalse($status);
    }
}
