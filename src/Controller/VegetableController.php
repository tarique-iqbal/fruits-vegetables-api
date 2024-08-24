<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Vegetable;
use App\Helper\PaginationHelper;
use App\Repository\VegetableRepository;
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
        private readonly VegetableRepository $vegetableRepository
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

        $this->vegetableRepository->add($vegetable);

        return $this->json($vegetable, Response::HTTP_CREATED);
    }

    #[Route('/vegetables/{page}', name: 'vegetable_list', requirements: ['page' => '\d+'], methods: ['GET'])]
    public function getVegetables(int $page = 1): JsonResponse
    {
        $query = $this->vegetableRepository->getQuery();
        $pager = (new PaginationHelper($query))->paginate($page);
        $vegetables = $this->vegetableRepository->getPaginatedResult($query, $pager->offset, $pager->limit);

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
        $vegetable = $this->vegetableRepository->find($id);

        if ($vegetable === null) {
            throw new NotFoundHttpException(
                sprintf('Vegetable does not found. id: %s', $id)
            );
        }

        $this->vegetableRepository->remove($vegetable);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
