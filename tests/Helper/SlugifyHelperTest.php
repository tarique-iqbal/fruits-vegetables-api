<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Helper\SlugifyHelper;
use PHPUnit\Framework\TestCase;

class SlugifyHelperTest extends TestCase
{
    public static function stringProvider(): array
    {
        return [
            [
                'Carrot', 'carrot'
            ],
            [
                'Mustard Greens', 'mustard-greens'
            ],
            [
                'Aubergene (eggplant)', 'aubergene-eggplant'
            ],
        ];
    }

    /**
     * @dataProvider stringProvider
     */
    public function testGetTotalPriceWithoutPromotion(string $string, string $expectedResult): void
    {
        $slugifyString = (new SlugifyHelper())->slugify($string);

        $this->assertSame($expectedResult, $slugifyString);
    }
}
