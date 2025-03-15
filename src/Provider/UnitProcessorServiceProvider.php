<?php

declare(strict_types=1);

namespace App\Provider;

use App\Service\UnitProcessor\UnitProcessorServiceInterface;

final class UnitProcessorServiceProvider
{
    private array $unitProcessors;

    public function __construct(iterable $unitProcessors)
    {
        $this->populateUnitProcessors($unitProcessors);
    }

    /**
     * @return UnitProcessorServiceInterface[]
     */
    public function getAll(): array
    {
        return $this->unitProcessors;
    }

    private function populateUnitProcessors(iterable $unitProcessors): void
    {
        foreach ($unitProcessors as $unitProcessor) {
            $this->unitProcessors[$unitProcessor->getType()] = $unitProcessor;
        }
    }
}
