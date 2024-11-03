<?php

declare(strict_types=1);

namespace App\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class FileExistsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof FileExists) {
            throw new UnexpectedTypeException($constraint, FileExists::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (file_exists($value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ file }}', $value)
            ->addViolation();
    }
}
