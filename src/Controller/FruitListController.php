<?php

declare(strict_types=1);

namespace App\Controller;

use App\Mapper\MapperInterface;
use App\Service\FruitServiceInterface;
use App\Service\ValidationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;

final class FruitListController extends AbstractController
{
    private const DEFAULT_UNIT = 'gram';

    private const UNIT_LIST = ['gram', 'kilogram'];

    public function __construct(
        private readonly ValidationServiceInterface $validationService,
        private readonly MapperInterface $fruitMapper,
        private readonly FruitServiceInterface $fruitService,
    ) {
    }

    #[Route('/fruits', name: 'fruit_list', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $page = $request->query->get('page', 1);
        $unit = $request->query->get('unit', self::DEFAULT_UNIT);

        $this->validationService->validateRawValue([
            ['page', $page, new Assert\Regex(pattern: '/^[1-9]\d*$/', match: true)],
            ['unit', $unit, new Assert\Choice(self::UNIT_LIST)]
        ]);

        $result = $this->fruitService->getPaginatedFruits((int) $page);
        $result['fruits'] = $this->fruitMapper->mapAllToDto($result['fruits']);

        return $this->json(
            data: $result,
            status: Response::HTTP_OK,
            context: ['groups' => [$unit, 'list']]
        );
    }
}
