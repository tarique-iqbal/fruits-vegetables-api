<?php

declare(strict_types=1);

namespace App\Tests\Provider;

use App\Provider\UnitProcessorServiceProvider;
use App\Service\UnitProcessor\FruitProcessorService;
use App\Service\UnitProcessor\VegetableProcessorService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UnitProcessorServiceProviderTest extends KernelTestCase
{
    private UnitProcessorServiceProvider $unitProcessorServiceProvider;

    public function setUp(): void
    {
        $container = static::getContainer();

        $this->unitProcessorServiceProvider = $container->get(UnitProcessorServiceProvider::class);
    }

    public function testGet(): void
    {
        $expectedClasses = [
            'fruit' => FruitProcessorService::class,
            'vegetable' => VegetableProcessorService::class,
        ];
        $unitProcessors = $this->unitProcessorServiceProvider->getAll();

        foreach ($expectedClasses as $key => $expectedClass) {
            $this->assertInstanceOf($expectedClass, $unitProcessors[$key]);
        }
    }
}
