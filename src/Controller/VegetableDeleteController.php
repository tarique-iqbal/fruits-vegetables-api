<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\VegetableServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class VegetableDeleteController extends AbstractController
{
    public function __construct(
        private readonly VegetableServiceInterface $vegetableService,
    ) {
    }

    #[Route('/vegetables/{id}', name: 'vegetable_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function __invoke(int $id): JsonResponse
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
