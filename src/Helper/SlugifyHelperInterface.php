<?php

declare(strict_types=1);

namespace App\Helper;

interface SlugifyHelperInterface
{
    public function slugify(string $string): string;
}
