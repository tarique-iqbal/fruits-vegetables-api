<?php

declare(strict_types=1);

namespace App\Component\Validator\Constraints;

use App\Entity\Vegetable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueVegetableValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueVegetable) {
            throw new UnexpectedTypeException($constraint, UniqueVegetable::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        $vegetable = $this->entityManager->getRepository(Vegetable::class)
            ->findOneBy(['alias' => $value]);

        if ($vegetable !== null) {
            $this->context->buildViolation($constraint->message)
                ->setCode(UniqueVegetable::NOT_UNIQUE_ERROR)
                ->addViolation();
        }
    }
}
