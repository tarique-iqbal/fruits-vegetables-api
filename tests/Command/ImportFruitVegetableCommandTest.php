<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Component\Validator\Exception\AcceptanceFailedException;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ImportFruitVegetableCommandTest extends KernelTestCase
{
    private CommandTester $commandTester;

    private vfsStreamDirectory $vfsRoot;

    public function setUp(): void
    {
        $application = new Application(self::bootKernel());
        $command = $application->find('app:import-fruit-vegetable');
        $this->commandTester = new CommandTester($command);

        parent::setUp();
    }

    public function testExecute(): void
    {
        $this->createVirtualFile('request.json');

        $this->commandTester->execute(['file' => $this->vfsRoot->url() . '/data/request.json']);
        $output = $this->commandTester->getDisplay();

        $this->commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString('app:import-fruit-vegetable', $output);
    }

    public static function invalidFileProvider(): array
    {
        return [
            ['request.json', 'non-exist-file.json'],
            ['disallow-file-extension.txt', 'disallow-file-extension.txt'],
        ];
    }

    /**
     * @dataProvider invalidFileProvider
     */
    public function testExecuteInvalidFile(string $fileHas, string $fileProvide): void
    {
        $this->createVirtualFile($fileHas);

        $this->expectException(AcceptanceFailedException::class);

        $this->commandTester->execute(['file' => $this->vfsRoot->url() . $fileProvide]);
    }

    private function createVirtualFile(string $fileName): void
    {
        $structure = [
            'data' => [
                $fileName => '[
                    {"id": 1,"name": "Apples","type": "fruit","quantity": 20,"unit": "kg"},
                    {"id": 2,"name": "Carrot","type": "vegetable","quantity": 10922,"unit": "g"}
               ]'
            ]
        ];

        $this->vfsRoot = vfsStream::setup(sys_get_temp_dir(), null, $structure);
    }
}
