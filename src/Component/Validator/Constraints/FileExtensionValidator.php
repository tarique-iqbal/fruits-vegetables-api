<?php

declare(strict_types=1);

namespace App\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class FileExtensionValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof FileExtension) {
            throw new UnexpectedTypeException($constraint, FileExtension::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $extension = pathinfo($value, PATHINFO_EXTENSION);

        if ($constraint->extension === $extension) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ extension }}', $constraint->extension)
            ->setParameter('{{ received }}', $extension)
            ->addViolation();
    }
}
