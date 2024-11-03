<?php

declare(strict_types=1);

namespace App\Command;

use App\Component\Validator\Constraints as AppAssert;
use App\Provider\UnitProcessorServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(name: 'app:import-fruit-vegetable')]
class ImportFruitVegetableCommand extends AbstractUniqueCommand
{
    public function __construct(
        ValidatorInterface $validator,
        private readonly string $projectDir,
        private readonly UnitProcessorServiceProvider $unitProcessorProvider,
    ) {
        parent::__construct($validator);
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
        $file = $this->projectDir . '/' . $input->getArgument('file');

        $this->validateRawValue([
            ['file', $file, [new AppAssert\FileExists(), new AppAssert\FileExtension('json')]]
        ]);

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
