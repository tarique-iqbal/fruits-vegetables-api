<?php

declare(strict_types=1);

namespace App\Helper;

interface ValidationHelperInterface
{
    public function validate($entity): bool;

    public function getErrorMessages(): array;
}
