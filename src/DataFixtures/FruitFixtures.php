<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Fruit;
use App\Helper\SlugifyHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

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
        $slugifyService = new SlugifyHelper();

        foreach (self::FRUITS as $fruitData) {
            $fruit = new Fruit();
            $fruit->setName($fruitData['name'])
                ->setAlias($slugifyService->slugify($fruitData['name']))
                ->setGram($fruitData['gram']);
            $manager->persist($fruit);
        }
        $manager->flush();
    }
}
