<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Vegetable;
use App\Helper\SlugifyHelper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VegetableFixtures extends Fixture
{
    private const VEGETABLE = [
        [
            "name" => "Carrot",
            "gram" => 10922
        ],
        [
            "name" => "Beans",
            "gram" => 65000
        ],
        [
            "name" => "Beetroot",
            "gram" => 950
        ],
        [
            "name" => "Broccoli",
            "gram" => 3000
        ],
        [
            "name" => "Tomatoes",
            "gram" => 5000
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        $slugifyService = new SlugifyHelper();

        foreach (self::VEGETABLE as $vegetableData) {
            $vegetable = new Vegetable();
            $vegetable->setName($vegetableData['name'])
                ->setAlias($slugifyService->slugify($vegetableData['name']))
                ->setGram($vegetableData['gram']);
            $manager->persist($vegetable);
        }
        $manager->flush();
    }
}
