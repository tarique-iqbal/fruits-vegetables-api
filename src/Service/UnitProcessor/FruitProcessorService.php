<?php

declare(strict_types=1);

namespace App\Service\UnitProcessor;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use App\Utility\Utility;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class FruitProcessorService implements UnitProcessorServiceInterface
{
    private const TYPE = 'fruit';

    public function __construct(
        private SluggerInterface $asciiSlugger,
        private FruitRepository $fruitRepository,
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

        if ($this->fruitRepository->existsByAlias($alias)) {
            return false;
        }

        $gram = Utility::convertToGram($object->unit, $object->quantity);

        $fruit = new Fruit();
        $fruit->setName($object->name)
            ->setAlias($alias)
            ->setGram($gram);

        $this->entityManager->persist($fruit);

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
