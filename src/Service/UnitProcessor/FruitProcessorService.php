<?php

declare(strict_types=1);

namespace App\Service\UnitProcessor;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class FruitProcessorService implements UnitProcessorServiceInterface
{
    public function __construct(
        private SluggerInterface $asciiSlugger,
        private FruitRepository $fruitRepository,
    ) {
    }

    public function isMatch(string $type): bool
    {
        return ($type === 'fruit');
    }

    public function process(\stdClass $object): bool
    {
        $alias = $this->asciiSlugger->slug($object->name)
            ->lower()
            ->toString();
        $fruit = $this->fruitRepository->findOneBy(['alias' => $alias]);

        if ($fruit instanceof Fruit) {
            return false;
        }

        $gram = $object->unit === 'kg' ?
            $object->quantity * 1000 :
            $object->quantity;

        $fruit = new Fruit();
        $fruit->setName($object->name)
            ->setAlias($alias)
            ->setGram($gram);

        $this->fruitRepository->add($fruit);

        return true;
    }
}
