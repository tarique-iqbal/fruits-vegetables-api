<?php

declare(strict_types=1);

namespace App\Tests\Service\UnitProcessor;

use App\Service\UnitProcessor\UnitProcessorServiceInterface;
use App\Service\UnitProcessor\VegetableProcessorService;
use App\Tests\DataFixtures\VegetableFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VegetableProcessorServiceTest extends KernelTestCase
{
    private UnitProcessorServiceInterface $vegetableProcessorService;

    public function setUp(): void
    {
        $container = static::getContainer();
        $container->get(DatabaseToolCollection::class)
            ->get()
            ->loadFixtures([VegetableFixtures::class]);

        $this->vegetableProcessorService = $container->get(VegetableProcessorService::class);
    }


    public function testProcess(): void
    {
        $object = json_decode('{"id":13,"name":"Cucumber","type":"vegetable","quantity":8,"unit":"kg"}');
        $status = $this->vegetableProcessorService->process($object);

        $this->assertTrue($status);
    }

    public function testProcessVegetableExistInDatabase(): void
    {
        $object = json_decode('{"id":1,"name":"Carrot","type":"vegetable","quantity":10922,"unit":"g"}');
        $status = $this->vegetableProcessorService->process($object);

        $this->assertFalse($status);
    }
}
