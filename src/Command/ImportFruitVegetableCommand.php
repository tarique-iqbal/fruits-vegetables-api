<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\Validator\Constraints as AppAssert;
use App\Provider\UnitProcessorServiceProvider;
use App\Service\ValidationServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-fruit-vegetable')]
class ImportFruitVegetableCommand extends AbstractUniqueCommand
{
    private const BATCH_SIZE = 100;

    public function __construct(
        private readonly ValidationServiceInterface $validationService,
        private readonly UnitProcessorServiceProvider $unitProcessorProvider,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Import fruits and vegetables from given json file.')
            ->addArgument('file', InputArgument::REQUIRED, 'File relative path');
    }

    /**
     * @throws \JsonException
     */
    protected function perform(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf('Start command: %s', $this->getName()));
        $file = $input->getArgument('file');

        $this->validationService->validateRawValue([
            ['file', $file, [new AppAssert\FileExists(), new AppAssert\FileExtension(['json'])]]
        ]);

        $fruitsVegetables = json_decode(
            file_get_contents($file),
            false,
            512,
            JSON_THROW_ON_ERROR
        );

        $progressBar = new ProgressBar($output, count($fruitsVegetables));
        $progressBar->start();

        $unitProcessors = $this->unitProcessorProvider->getAll();

        $counter = 1;
        foreach ($fruitsVegetables as $object) {
            if (array_key_exists($object->type, $unitProcessors)) {
                $unitProcessor = $unitProcessors[$object->type];

                $isFlush = $counter % self::BATCH_SIZE === 0;
                $status = $unitProcessor->process($object, $isFlush);

                if ($status === false) {
                    $this->logger->warning(
                        sprintf('Duplicate entry: %s %s!', $object->type, $object->name)
                    );
                }

                $counter++;
            } else {
                $this->logger->alert(
                    sprintf('Unit processor type "%s" not found.', $object->type)
                );
            }

            $progressBar->advance();
        }

        current($unitProcessors)->flush();

        $progressBar->finish();
        $output->writeln(sprintf('%sExit command: %s', PHP_EOL, $this->getName()));

        return self::SUCCESS;
    }
}
