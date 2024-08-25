<?php

declare(strict_types=1);

namespace App\Command;

use App\Provider\UnitProcessorServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

#[AsCommand(name: 'app:import-fruit-vegetable')]
class ImportFruitVegetableCommand extends Command
{
    public function __construct(
        private readonly string $projectDir,
        private readonly UnitProcessorServiceProvider $unitProcessorProvider
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
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(sprintf('Start command: %s', $this->getName()));

        $file = $this->projectDir . '/' . $input->getArgument('file');

        Assert::fileExists($file, sprintf('File "%s" not found.', $file));
        Assert::endsWith($file, '.json', 'Invalid file extension.');

        $fruitsVegetables = json_decode(
            file_get_contents($file),
            false,
            512,
            JSON_THROW_ON_ERROR
        );

        foreach ($fruitsVegetables as $object) {
            $status = $this->unitProcessorProvider
                ->get($object->type)
                ?->process($object);

            if ($status === true) {
                $output->writeln(
                    sprintf('Successfully added %s: %s', $object->type, $object->name)
                );
            }
        }

        $output->writeln(sprintf('Exit command: %s', $this->getName()));

        return self::SUCCESS;
    }
}
