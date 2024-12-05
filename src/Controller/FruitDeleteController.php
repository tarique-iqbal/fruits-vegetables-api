<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FruitServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class FruitDeleteController extends AbstractController
{
    public function __construct(
        private readonly FruitServiceInterface $fruitService,
    ) {
    }

    #[Route('/fruits/{id}', name: 'fruit_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function __invoke(int $id): JsonResponse
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
