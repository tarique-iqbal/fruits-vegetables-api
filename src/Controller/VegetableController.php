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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class VegetableController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        SluggerInterface $asciiSlugger,
        private readonly ValidationServiceInterface $validationService,
        private readonly VegetableServiceInterface $vegetableService,
    ) {
        parent::__construct($serializer, $asciiSlugger);
    }

    protected function getDtoClassName(): string
    {
        return VegetableDto::class;
    }

    #[Route('/vegetables', name: 'vegetable_add', methods: ['POST'])]
    public function postVegetable(
        Request $request,
        MapperInterface $vegetableMapper,
    ): JsonResponse {
        $vegetableDto = $this->loadDto($request);

        $this->validationService->validate($vegetableDto);

        $vegetable = $vegetableMapper->mapToEntity($vegetableDto);

        $this->vegetableService->addVegetable($vegetable);

        return $this->json($vegetable, Response::HTTP_CREATED);
    }

    #[Route('/vegetables', name: 'vegetable_list', methods: ['GET'])]
    public function getVegetables(
        Request $request,
        MapperInterface $vegetableMapper,
    ): JsonResponse {
        $page = $request->query->get('page', 1);
        $unit = $request->query->get('unit', self::DEFAULT_UNIT);

        $this->validationService->validateRawValue([
            ['page', $page, new Assert\Regex(pattern: '/^[1-9]\d*$/', match: true)],
            ['unit', $unit, new Assert\Choice(self::UNIT_LIST)]
        ]);

        $result = $this->vegetableService->getPaginatedVegetables((int) $page);
        $result['vegetables'] = $vegetableMapper->mapAllToDto($result['vegetables']);

        return $this->json(
            data: $result,
            status: Response::HTTP_OK,
            context: ['groups' => [$unit, 'list']]
        );
    }

    #[Route('/vegetables/{id}', name: 'vegetable_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteVegetable(int $id): JsonResponse
    {
        $vegetable = $this->vegetableService->findById($id);

        if ($vegetable === null) {
            throw new NotFoundHttpException(
                sprintf('Vegetable does not found. id: %s', $id)
            );
        }

        $this->vegetableService->removeVegetable($vegetable);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
