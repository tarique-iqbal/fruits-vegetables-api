<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use App\Helper\PaginationHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FruitController extends AbstractController
{
    public function __construct(
        private readonly FruitRepository $fruitRepository
    ) {
    }

    #[Route('/fruits', name: 'fruit_add', methods: ['POST'])]
    public function postFruit(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SluggerInterface $asciiSlugger
    ): JsonResponse {
        $fruit = $serializer->deserialize(
            $request->getContent(),
            Fruit::class,
            'json'
        );

        $alias = $asciiSlugger->slug($fruit->getName())
            ->lower()
            ->toString();
        $fruit->setAlias($alias);

        $violations = $validator->validate($fruit);
        if (count($violations) > 0) {
            throw new ValidationFailedException(null, $violations);
        }

        $this->fruitRepository->add($fruit);

        return $this->json($fruit, Response::HTTP_CREATED);
    }

    #[Route('/fruits/{page}', name: 'fruit_list', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function getFruits(
        PaginationHelperInterface $paginationService,
        int $page = 1
    ): JsonResponse {
        $query = $this->fruitRepository->getQuery();
        $pager = $paginationService->paginate($query, $page);
        $fruits = $this->fruitRepository->getPaginatedResultFromQuery($query, $pager->offset, $pager->limit);

        return $this->json(
            [
                'fruits' => $fruits,
                'pager' => $pager
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/fruits/{id}', name: 'fruit_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteFruit(int $id): JsonResponse
    {
        $fruit = $this->fruitRepository->find($id);

        if ($fruit === null) {
            throw new NotFoundHttpException(
                sprintf('Fruit does not found. id: %s', $id)
            );
        }

        $this->fruitRepository->remove($fruit);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
