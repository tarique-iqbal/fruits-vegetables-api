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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class FruitController extends AbstractApiController
{
    public function __construct(
        SerializerInterface $serializer,
        SluggerInterface $asciiSlugger,
        private readonly ValidationServiceInterface $validationService,
        private readonly FruitServiceInterface $fruitService,
    ) {
        parent::__construct($serializer, $asciiSlugger);
    }

    protected function getDtoClassName(): string
    {
        return FruitDto::class;
    }

    #[Route('/fruits', name: 'fruit_add', methods: ['POST'])]
    public function postFruit(
        Request $request,
        MapperInterface $fruitMapper,
    ): JsonResponse {
        $fruitDto = $this->loadDto($request);

        $this->validationService->validate($fruitDto);

        $fruit = $fruitMapper->mapToEntity($fruitDto);

        $this->fruitService->addFruit($fruit);

        return $this->json($fruit, Response::HTTP_CREATED);
    }

    #[Route('/fruits', name: 'fruit_list', methods: ['GET'])]
    public function getFruits(
        Request $request,
        MapperInterface $fruitMapper,
    ): JsonResponse {
        $page = $request->query->get('page', 1);
        $unit = $request->query->get('unit', self::DEFAULT_UNIT);

        $this->validationService->validateRawValue([
            ['page', $page, new Assert\Regex(pattern: '/^[1-9]\d*$/', match: true)],
            ['unit', $unit, new Assert\Choice(self::UNIT_LIST)]
        ]);

        $result = $this->fruitService->getPaginatedFruits((int) $page);
        $result['fruits'] = $fruitMapper->mapAllToDto($result['fruits']);

        return $this->json(
            data: $result,
            status: Response::HTTP_OK,
            context: ['groups' => [$unit, 'list']]
        );
    }

    #[Route('/fruits/{id}', name: 'fruit_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteFruit(int $id): JsonResponse
    {
        $fruit = $this->fruitService->findById($id);

        if ($fruit === null) {
            throw new NotFoundHttpException(
                sprintf('Fruit does not found. id: %s', $id)
            );
        }

        $this->fruitService->removeFruit($fruit);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
