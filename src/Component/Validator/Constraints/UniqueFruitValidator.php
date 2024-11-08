<?php

declare(strict_types=1);

namespace App\Component\Validator\Constraints;

use App\Repository\FruitRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueFruitValidator extends ConstraintValidator
{
    public function __construct(private readonly FruitRepository $fruitRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueFruit) {
            throw new UnexpectedTypeException($constraint, UniqueFruit::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if ($this->fruitRepository->existsByAlias($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->setCode(UniqueFruit::NOT_UNIQUE_ERROR)
                ->addViolation();
        }
    }
}
