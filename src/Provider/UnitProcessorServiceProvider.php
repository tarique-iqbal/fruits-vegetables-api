<?php

declare(strict_types=1);

namespace App\Provider;

use App\Service\UnitProcessor\UnitProcessorServiceInterface;
use Psr\Log\LoggerInterface;

readonly class UnitProcessorServiceProvider
{
    public function __construct(
        private iterable $unitProcessors,
        private LoggerInterface $logger,
    ) {
    }

    public function get(string $type): ?UnitProcessorServiceInterface
    {
        foreach ($this->unitProcessors as $unitProcessor) {
            if ($unitProcessor->isMatch($type)) {
                return $unitProcessor;
            }
        }

        $this->logger->alert(
            sprintf('Unit processor type "%s" not found.', $type)
        );

        return null;
    }
}
