<?php

declare(strict_types=1);

namespace App\Service\UnitProcessor;

use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
use App\Utility\Utility;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class VegetableProcessorService implements UnitProcessorServiceInterface
{
    private const TYPE = 'vegetable';

    public function __construct(
        private SluggerInterface $asciiSlugger,
        private VegetableRepository $vegetableRepository,
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
        $vegetable = $this->vegetableRepository->findOneBy(['alias' => $alias]);

        if ($vegetable instanceof vegetable) {
            return false;
        }

        $gram = Utility::convertToGram($object->unit, $object->quantity);

        $vegetable = new vegetable();
        $vegetable->setName($object->name)
            ->setAlias($alias)
            ->setGram($gram);

        $this->vegetableRepository->add($vegetable);

        return true;
    }
}
