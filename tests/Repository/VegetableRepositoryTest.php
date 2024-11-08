<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
use App\Tests\DataFixtures\VegetableFixtures;
use Doctrine\ORM\Query;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VegetableRepositoryTest extends KernelTestCase
{
    private VegetableRepository $vegetableRepository;

    public function setUp(): void
    {
        $container = static::getContainer();
        $container->get(DatabaseToolCollection::class)
            ->get()
            ->loadFixtures([VegetableFixtures::class]);

        $this->vegetableRepository = $container->get(VegetableRepository::class);
    }

    public function testAdd(): void
    {
        $fruit = new Vegetable();
        $fruit->setName('Cucumber')
            ->setAlias('cucumber')
            ->setGram(10000);

        $this->vegetableRepository->add($fruit);

        $savedFruit = $this->vegetableRepository->findOneBy(['alias' => 'cucumber']);

        $this->assertNotNull($savedFruit);
        $this->assertSame('cucumber', $savedFruit->getAlias());
        $this->assertInstanceOf(Vegetable::class, $savedFruit);
    }

    public function testRemove(): void
    {
        $fruit = $this->vegetableRepository->findOneBy(['alias' => 'carrot']);
        $this->vegetableRepository->remove($fruit);

        $removedFruit = $this->vegetableRepository->findOneBy(['alias' => 'cucumber']);

        $this->assertNull($removedFruit);
        $this->assertInstanceOf(Vegetable::class, $fruit);
    }

    public function testExistsByAliasReturnsTrue(): void
    {
        $alias = 'cucumber';
        $fruit = new Vegetable();
        $fruit->setName('Cucumber')
            ->setAlias($alias)
            ->setGram(8000);

        $this->vegetableRepository->add($fruit);
        $status = $this->vegetableRepository->existsByAlias($alias);

        $this->assertTrue($status);
    }

    public function testExistsByAliasReturnsFalse(): void
    {
        $alias = 'non-exists';
        $status = $this->vegetableRepository->existsByAlias($alias);

        $this->assertFalse($status);
    }

    public function testGetQuery(): void
    {
        $query = $this->vegetableRepository->getQuery();

        $this->assertInstanceOf(Query::class, $query);
    }

    public function testGetPaginatedResult(): void
    {
        $query = $this->vegetableRepository->getQuery();
        $fruits = $this->vegetableRepository->getPaginatedResult($query, 0, 4);

        $this->assertContainsOnlyInstancesOf(Vegetable::class, $fruits);
    }
}
