<?php

declare(strict_types=1);

namespace App\Utility;

class Utility
{
    public static function convertToGram(string $unit, int $quantity): int
    {
        return $unit === 'kg' ?
            $quantity * 1000 :
            $quantity;
    }
}
