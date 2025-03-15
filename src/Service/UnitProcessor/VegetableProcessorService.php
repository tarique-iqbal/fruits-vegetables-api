<?php

declare(strict_types=1);

namespace App\Service\UnitProcessor;

use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
use App\Utility\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class VegetableProcessorService implements UnitProcessorServiceInterface
{
    private const TYPE = 'vegetable';

    public function __construct(
        private SluggerInterface $asciiSlugger,
        private VegetableRepository $vegetableRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function process(\stdClass $object, bool $isFlush): bool
    {
        $alias = $this->asciiSlugger->slug($object->name)
            ->lower()
            ->toString();

        if ($this->vegetableRepository->existsByAlias($alias)) {
            return false;
        }

        $gram = Utility::convertToGram($object->unit, $object->quantity);

        $vegetable = new vegetable();
        $vegetable->setName($object->name)
            ->setAlias($alias)
            ->setGram($gram);

        $this->entityManager->persist($vegetable);

        if ($isFlush) {
            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        return true;
    }

    public function flush(): void
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
