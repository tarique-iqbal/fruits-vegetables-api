<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\Request\VegetableDto;
use App\Mapper\MapperInterface;
use App\Service\ValidationServiceInterface;
use App\Service\VegetableServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class VegetableAddController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        SluggerInterface $asciiSlugger,
        private readonly ValidationServiceInterface $validationService,
        private readonly MapperInterface $vegetableMapper,
        private readonly VegetableServiceInterface $vegetableService,
    ) {
        parent::__construct($serializer, $asciiSlugger);
    }

    protected function getDtoClassName(): string
    {
        return VegetableDto::class;
    }

    #[Route('/vegetables', name: 'vegetable_add', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $vegetableDto = $this->loadDto($request);

        $this->validationService->validate($vegetableDto);

        $vegetable = $this->vegetableMapper->mapToEntity($vegetableDto);

        $this->vegetableService->addVegetable($vegetable);

        return $this->json($vegetable, Response::HTTP_CREATED);
    }
}
