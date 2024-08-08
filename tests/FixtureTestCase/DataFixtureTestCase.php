<?php

declare(strict_types=1);

namespace App\Tests\FixtureTestCase;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;

abstract class DataFixtureTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        self::runCommand('doctrine:fixtures:load --no-interaction');

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        parent::setUp();
    }

    protected static function runCommand(string $command): ?int
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication(): Application
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        return $application;
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();

        parent::tearDown();
    }
}
