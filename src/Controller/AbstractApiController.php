<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Validator\Exception\AcceptanceFailedException;
use App\Dto\Request\FruitDto;
use App\Dto\Request\VegetableDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractApiController extends AbstractController
{
    protected const DEFAULT_UNIT = 'gram';

    protected const UNIT_LIST = ['gram', 'kilogram'];

    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
        private readonly SluggerInterface $asciiSlugger,
    ) {
    }

    abstract protected function getDtoClassName(): string;

    protected function validateRawValue(array $collection): void
    {
        $input = [];
        $constraint = [];

        foreach ($collection as $item) {
            $input[$item[0]] = $item[1];
            $constraint[$item[0]] = $item[2];
        }

        $violations = $this->validator->validate(
            $input,
            new Assert\Collection($constraint)
        );

        if (count($violations) > 0) {
            throw new AcceptanceFailedException(null, $violations);
        }
    }

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
