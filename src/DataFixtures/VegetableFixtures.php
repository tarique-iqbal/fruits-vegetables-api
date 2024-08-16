<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Vegetable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

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
        $asciiSlugger = new AsciiSlugger();

        foreach (self::VEGETABLE as $vegetableData) {
            $vegetable = new Vegetable();
            $alias = $asciiSlugger->slug($vegetableData['name'])
                ->lower()
                ->toString();

            $vegetable->setName($vegetableData['name'])
                ->setAlias($alias)
                ->setGram($vegetableData['gram']);
            $manager->persist($vegetable);
        }
        $manager->flush();
    }
}
