<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ImportFruitVegetableCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    public function setUp(): void
    {
        $application = new Application(self::bootKernel());
        $command = $application->find('app:import-fruit-vegetable');
        $this->commandTester = new CommandTester($command);

        parent::setUp();
    }

    public function testExecute(): void
    {
        $this->commandTester->execute(['file' => 'tests/data/request.json']);
        $output = $this->commandTester->getDisplay();

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('app:import-fruit-vegetable', $output);
    }

    public function testExecuteFileNotFound(): void
    {
        $this->commandTester->execute(['file' => 'tests/data/fake.json']);
        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('File not found:', $output);
    }
}
