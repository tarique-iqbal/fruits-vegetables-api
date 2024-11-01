<?php

declare(strict_types=1);

namespace App\Provider;

use App\Service\UnitProcessor\UnitProcessorServiceInterface;
use Psr\Log\LoggerInterface;

final class UnitProcessorServiceProvider
{
    private array $unitProcessors;

    public function __construct(
        iterable $unitProcessors,
        private readonly LoggerInterface $logger,
    ) {
        $this->populateUnitProcessors($unitProcessors);
    }

    public function get(string $type): ?UnitProcessorServiceInterface
    {
        if (array_key_exists($type, $this->unitProcessors)) {
            return $this->unitProcessors[$type];
        }

        $this->logger->alert(
            sprintf('Unit processor type "%s" not found.', $type)
        );

        return null;
    }

    private function populateUnitProcessors(iterable $unitProcessors): void
    {
        foreach ($unitProcessors as $unitProcessor) {
            $this->unitProcessors[$unitProcessor->getType()] = $unitProcessor;
        }
    }
}
