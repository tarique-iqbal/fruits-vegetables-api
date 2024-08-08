<?php

declare(strict_types=1);

namespace App\Helper;

class SlugifyHelper implements SlugifyHelperInterface
{
    public function slugify(string $string): string
    {
        $string = str_replace(' ', '-', trim($string));
        $string = preg_replace('/[^A-Za-zÀ-ú\-]+/', '', $string);

        return strtolower($string);
    }
}
