<?php

declare(strict_types=1);

namespace App\Service\UnitProcessor;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use App\Utility\Utility;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class FruitProcessorService implements UnitProcessorServiceInterface
{
    private const TYPE = 'fruit';

    public function __construct(
        private SluggerInterface $asciiSlugger,
        private FruitRepository $fruitRepository,
    ) {
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function process(\stdClass $object): bool
    {
        $alias = $this->asciiSlugger->slug($object->name)
            ->lower()
            ->toString();

        if ($this->fruitRepository->existsByAlias($alias)) {
            return false;
        }

        $gram = Utility::convertToGram($object->unit, $object->quantity);

        $fruit = new Fruit();
        $fruit->setName($object->name)
            ->setAlias($alias)
            ->setGram($gram);

        $this->fruitRepository->add($fruit);

        return true;
    }
}
