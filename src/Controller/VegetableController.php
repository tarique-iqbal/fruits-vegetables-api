<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Vegetable;
use App\Repository\VegetableRepository;
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
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class VegetableController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/vegetables', name: 'vegetable_add', methods: ['POST'])]
    public function postVegetable(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        SluggerInterface $asciiSlugger
    ): JsonResponse {
        $vegetable = $serializer->deserialize(
            $request->getContent(),
            Vegetable::class,
            'json'
        );

        $alias = $asciiSlugger->slug($vegetable->getName())
            ->lower()
            ->toString();
        $vegetable->setAlias($alias);

        $violations = $validator->validate($vegetable);
        if (count($violations) > 0) {
            throw new ValidationFailedException(null, $violations);
        }

        $this->entityManager->persist($vegetable);
        $this->entityManager->flush();

        return $this->json($vegetable, Response::HTTP_CREATED);
    }

    #[Route('/vegetables/{page}', name: 'vegetable_list', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function getVegetables(
        VegetableRepository $vegetableRepository,
        PaginationHelperInterface $paginationService,
        int $page = 1
    ): JsonResponse {
        $query = $vegetableRepository->getQuery();
        $pager = $paginationService->paginate($query, $page);
        $vegetables = $vegetableRepository->getPaginatedResultFromQuery($query, $pager->offset, $pager->limit);

        return $this->json(
            [
                'vegetables' => $vegetables,
                'pager' => $pager
            ],
            Response::HTTP_OK
        );
    }

    #[Route('/vegetables/{id}', name: 'vegetable_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function deleteVegetable(int $id): JsonResponse
    {
        $vegetable = $this->entityManager
            ->getRepository(Vegetable::class)
            ->find($id);

        if ($vegetable === null) {
            throw new NotFoundHttpException(
                sprintf('Vegetable does not found. id: %s', $id)
            );
        }

        $this->entityManager->remove($vegetable);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
