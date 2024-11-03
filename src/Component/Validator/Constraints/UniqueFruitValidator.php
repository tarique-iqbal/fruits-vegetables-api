<?php

declare(strict_types=1);

namespace App\Component\Validator\Constraints;

use App\Entity\Fruit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UniqueFruitValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
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

        $fruit = $this->entityManager->getRepository(Fruit::class)
            ->findOneBy(['alias' => $value]);

        if ($fruit !== null) {
            $this->context->buildViolation($constraint->message)
                ->setCode(UniqueFruit::NOT_UNIQUE_ERROR)
                ->addViolation();
        }
    }
}
