<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Request\FruitDto;
use App\Mapper\MapperInterface;
use App\Service\FruitServiceInterface;
use App\Service\ValidationServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class FruitAddController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        SluggerInterface $asciiSlugger,
        private readonly ValidationServiceInterface $validationService,
        private readonly MapperInterface $fruitMapper,
        private readonly FruitServiceInterface $fruitService,
    ) {
        parent::__construct($serializer, $asciiSlugger);
    }

    protected function getDtoClassName(): string
    {
        return FruitDto::class;
    }

    #[Route('/fruits', name: 'fruit_add', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $fruitDto = $this->loadDto($request);

        $this->validationService->validate($fruitDto);

        $fruit = $this->fruitMapper->mapToEntity($fruitDto);

        $this->fruitService->addFruit($fruit);

        return $this->json($fruit, Response::HTTP_CREATED);
    }
}
