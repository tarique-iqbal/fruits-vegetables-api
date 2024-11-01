<?php

declare(strict_types=1);

namespace App\Service\UnitProcessor;

interface UnitProcessorServiceInterface
{
    public function getType(): string;

    public function process(\stdClass $object): bool;
}
