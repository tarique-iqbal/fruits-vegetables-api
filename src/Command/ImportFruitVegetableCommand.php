<?php

declare(strict_types=1);

namespace App\Command;

use App\Provider\UnitProcessorServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-fruit-vegetable')]
class ImportFruitVegetableCommand extends Command
{
    private string $message;

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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Start command: ' . $this->getName());

        $file = $this->projectDir . '/' . $input->getArgument('file');

        if ($this->validateFile($file) === false) {
            $output->writeln($this->getMessage());

            return self::FAILURE;
        }

        $fruitsVegetables = json_decode(file_get_contents($file));

        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln('Error: ' . json_last_error_msg());

            return self::FAILURE;
        }

        foreach ($fruitsVegetables as $object) {
            $status = $this->unitProcessorProvider
                ->get($object->type)
                ?->process($object);

            if ($status === true) {
                $output->writeln(
                    sprintf('Adding %s: %s', $object->type, $object->name)
                );
            }
        }

        $output->writeln('Exit command: ' . $this->getName());

        return Command::SUCCESS;
    }

    private function validateFile(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            $this->setMessage('File not found: ' . $filePath);

            return false;
        }

        $pathParts = pathinfo($filePath);
        if ($pathParts['extension'] !== 'json') {
            $this->setMessage('Invalid file extension: ' . $pathParts['extension']);

            return false;
        }

        return true;
    }

    private function setMessage(string $message): void
    {
        $this->message = $message;
    }

    private function getMessage(): string
    {
        return $this->message;
    }
}
