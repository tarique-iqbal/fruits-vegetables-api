<?php

declare(strict_types=1);

namespace App\Service;

interface ValidationServiceInterface
{
    public function validate(object $dto): void;

    public function validateRawValue(array $collection): void;
}
