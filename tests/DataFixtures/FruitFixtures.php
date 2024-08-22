<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\Entity\Fruit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FruitFixtures extends Fixture
{
    private const FRUITS = [
        [
            "name" => "Apples",
            "gram" => 20000
        ],
        [
            "name" => "Pears",
            "gram" => 3500
        ],
        [
            "name" => "Melons",
            "gram" => 120000
        ],
        [
            "name" => "Berries",
            "gram" => 10000
        ],
        [
            "name" => "Bananas",
            "gram" => 100000
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        $asciiSlugger = new AsciiSlugger();

        foreach (self::FRUITS as $fruitData) {
            $fruit = new Fruit();
            $alias = $asciiSlugger->slug($fruitData['name'])
                ->lower()
                ->toString();

            $fruit->setName($fruitData['name'])
                ->setAlias($alias)
                ->setGram($fruitData['gram']);
            $manager->persist($fruit);
        }
        $manager->flush();
    }
}
