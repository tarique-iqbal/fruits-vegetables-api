<?php

declare(strict_types=1);

namespace App\Tests\FixtureTest;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

abstract class FixtureAwareTestCase extends WebTestCase
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
        $kernel = self::getContainer()->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        return $application;
    }

    protected function tearDown(): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeQuery($platform->getTruncateTableSQL('fruit', true));
        $connection->executeQuery($platform->getTruncateTableSQL('vegetable', true));

        $this->entityManager->close();

        parent::tearDown();
    }
}
