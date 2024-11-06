<?php

declare(strict_types=1);

namespace App\Tests\Utility;

use App\Utility\Utility;
use PHPUnit\Framework\TestCase;

class UtilityTest extends TestCase
{
    public static function unitProvider(): array
    {
        return [
            ['kg', 20, 20000],
            ['g', 3500, 3500],
        ];
    }

    /**
     * @dataProvider unitProvider
     */
    public function testConvertToGram(string $unit, int $quantity, $expectedGram): void
    {
        $gram = Utility::convertToGram($unit, $quantity);

        $this->assertSame($expectedGram, $gram);
    }
}
