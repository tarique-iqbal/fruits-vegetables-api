<?php

declare(strict_types=1);

namespace App\Component\Validator\Constraints;

use App\Repository\VegetableRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueVegetableValidator extends ConstraintValidator
{
    public function __construct(private readonly VegetableRepository $vegetableRepository)
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

        if ($this->vegetableRepository->existsByAlias($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->setCode(UniqueVegetable::NOT_UNIQUE_ERROR)
                ->addViolation();
        }
    }
}
