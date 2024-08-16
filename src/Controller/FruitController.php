<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Fruit;
use App\Helper\ValidationHelperInterface;
use App\Repository\FruitRepository;
use App\Helper\PaginationHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class FruitController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/fruits', name: 'fruit_add', methods: ['POST'])]
    public function postFruit(
        Request $request,
        SerializerInterface $serializer,
        ValidationHelperInterface $validationHelper,
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

        if ($validationHelper->validate($fruit) === false) {
            return $this->json([$validationHelper->getErrorMessages(), Response::HTTP_BAD_REQUEST]);
        }

        $this->entityManager->persist($fruit);
        $this->entityManager->flush();

        return $this->json([$fruit, Response::HTTP_CREATED]);
    }

    #[Route('/fruits/{page}', name: 'fruit_list', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function getFruits(
        FruitRepository $fruitRepository,
        PaginationHelperInterface $paginationService,
        int $page = 1
    ): JsonResponse {
        $query = $fruitRepository->getQuery();
        $pager = $paginationService->paginate($query, $page);
        $fruits = $fruitRepository->getPaginatedResultFromQuery($query, $pager->offset, $pager->limit);

        return $this->json([
            [
                'fruits' => $fruits,
                'pager' => $pager
            ],
            Response::HTTP_OK
        ]);
    }

    #[Route('/fruits/{id}', name: 'fruit_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteFruit(int $id): JsonResponse
    {
        $fruit = $this->entityManager
            ->getRepository(Fruit::class)
            ->find($id);

        if ($fruit === null) {
            throw new NotFoundHttpException(
                sprintf('Fruit does not found. id: %s', $id),
                null,
                Response::HTTP_NOT_FOUND
            );
        }

        $this->entityManager->remove($fruit);
        $this->entityManager->flush();

        return $this->json([null, Response::HTTP_NO_CONTENT]);
    }
}
