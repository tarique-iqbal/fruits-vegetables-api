<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsCommand(name: 'app:import-fruit-vegetable')]
class ImportFruitVegetableCommand extends Command
{
    private string $message;

    public function __construct(
        private readonly string $projectDir,
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface $asciiSlugger
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
        $file = $input->getArgument('file');
        $filePath = $this->projectDir . '/' . $file;

        if ($this->validateFile($filePath) === false) {
            $output->writeln($this->getMessage());

            return self::FAILURE;
        }

        $fruits = json_decode(file_get_contents($filePath));

        if (json_last_error() !== JSON_ERROR_NONE) {
            $output->writeln('Error: ' . json_last_error_msg());

            return self::FAILURE;
        }

        foreach ($fruits as $object) {
            $alias = $this->asciiSlugger->slug($object->name)
                ->lower()
                ->toString();

            if ($object->type === 'fruit') {
                $fruit = $this->entityManager->getRepository(Fruit::class)->findOneBy(['alias' => $alias]);
                if ($fruit === null) {
                    $output->writeln('Adding fruit: ' . $object->name);
                    $this->persistFruit($object, $alias);
                }
            } elseif ($object->type === 'vegetable') {
                $vegetable = $this->entityManager->getRepository(Vegetable::class)->findOneBy(['alias' => $alias]);
                if ($vegetable === null) {
                    $output->writeln('Adding vegetable: ' . $object->name);
                    $this->persistVegetable($object, $alias);
                }
            }
        }
        $this->entityManager->flush();

        $output->writeln('Exit command: ' . $this->getName());

        return Command::SUCCESS;
    }

    private function persistFruit(\stdClass $object, string $alias): void
    {
        $gram = $object->unit === 'kg' ? $object->quantity * 1000 : $object->quantity;

        $fruit = new Fruit();
        $fruit->setName($object->name)
            ->setAlias($alias)
            ->setGram($gram);
        $this->entityManager->persist($fruit);
    }

    private function persistVegetable(\stdClass $object, string $alias): void
    {
        $gram = $object->unit === 'kg' ? $object->quantity * 1000 : $object->quantity;

        $vegetable = new Vegetable();
        $vegetable->setName($object->name)
            ->setAlias($alias)
            ->setGram($gram);
        $this->entityManager->persist($vegetable);
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
