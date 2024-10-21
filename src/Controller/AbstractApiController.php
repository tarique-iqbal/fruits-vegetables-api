<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Request\FruitDto;
use App\Dto\Request\VegetableDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

abstract class AbstractApiController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly SluggerInterface $asciiSlugger
    ) {
    }

    abstract protected function getDtoClassName(): string;

    protected function loadDto(Request $request): FruitDto|VegetableDto
    {
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            $this->getDtoClassName(),
            'json'
        );

        $alias = $this->asciiSlugger->slug($dto->getName())
            ->lower()
            ->toString();
        $dto->setAlias($alias);

        return $dto;
    }
}
