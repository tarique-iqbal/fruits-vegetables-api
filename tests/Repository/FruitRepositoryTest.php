<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use App\Tests\DataFixtures\FruitFixtures;
use Doctrine\ORM\Query;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FruitRepositoryTest extends KernelTestCase
{
    private FruitRepository $fruitRepository;

    public function setUp(): void
    {
        $container = static::getContainer();
        $container->get(DatabaseToolCollection::class)
            ->get()
            ->loadFixtures([FruitFixtures::class]);

        $this->fruitRepository = $container->get(FruitRepository::class);
    }

    public function testAdd(): void
    {
        $fruit = new Fruit();
        $fruit->setName('Kiwi')
            ->setAlias('kiwi')
            ->setGram(10000);

        $this->fruitRepository->add($fruit);

        $savedFruit = $this->fruitRepository->findOneBy(['alias' => 'kiwi']);

        $this->assertNotNull($savedFruit);
        $this->assertSame('kiwi', $savedFruit->getAlias());
        $this->assertInstanceOf(Fruit::class, $savedFruit);
    }

    public function testRemove(): void
    {
        $fruit = $this->fruitRepository->findOneBy(['alias' => 'apples']);
        $this->fruitRepository->remove($fruit);

        $removedFruit = $this->fruitRepository->findOneBy(['alias' => 'kiwi']);

        $this->assertNull($removedFruit);
        $this->assertInstanceOf(Fruit::class, $fruit);
    }

    public function testExistsByAliasReturnsTrue(): void
    {
        $alias = 'kiwi';
        $fruit = new Fruit();
        $fruit->setName('Kiwi')
            ->setAlias($alias)
            ->setGram(10000);

        $this->fruitRepository->add($fruit);
        $status = $this->fruitRepository->existsByAlias($alias);

        $this->assertTrue($status);
    }

    public function testExistsByAliasReturnsFalse(): void
    {
        $alias = 'non-exists';
        $status = $this->fruitRepository->existsByAlias($alias);

        $this->assertFalse($status);
    }

    public function testGetQuery(): void
    {
        $query = $this->fruitRepository->getQuery();

        $this->assertInstanceOf(Query::class, $query);
    }

    public function testGetPaginatedResult(): void
    {
        $query = $this->fruitRepository->getQuery();
        $fruits = $this->fruitRepository->getPaginatedResult($query, 0, 4);

        $this->assertContainsOnlyInstancesOf(Fruit::class, $fruits);
    }
}
