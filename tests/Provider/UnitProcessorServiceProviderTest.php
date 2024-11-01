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

    public static function typeProvider(): array
    {
        return [
            [
                'fruit', FruitProcessorService::class
            ],
            [
                'vegetable', VegetableProcessorService::class
            ],
        ];
    }

    /**
     * @dataProvider typeProvider
     */
    public function testGet(string $type, $expectedClass): void
    {
        $unitProcessor = $this->unitProcessorServiceProvider->get($type);

        $this->assertInstanceOf($expectedClass, $unitProcessor);
    }

    public static function invalidTypeProvider(): array
    {
        return [
            ['fake'],
            ['random'],
        ];
    }

    /**
     * @dataProvider invalidTypeProvider
     */
    public function testGetInvalidType(string $type): void
    {
        $unitProcessor = $this->unitProcessorServiceProvider->get($type);

        $this->assertNull($unitProcessor);
    }
}
