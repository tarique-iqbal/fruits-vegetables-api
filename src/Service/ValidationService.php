<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\Validator\Exception\AcceptanceFailedException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class ValidationService implements ValidationServiceInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(object $dto): void
    {
        $violations = $this->validator->validate($dto);

        if (count($violations) > 0) {
            throw new ValidationFailedException(null, $violations);
        }
    }

    public function validateRawValue(array $collection): void
    {
        $input = [];
        $constraint = [];

        foreach ($collection as $item) {
            $input[$item[0]] = $item[1];
            $constraint[$item[0]] = $item[2];
        }

        $violations = $this->validator->validate(
            $input,
            new Assert\Collection($constraint)
        );

        if (count($violations) > 0) {
            throw new AcceptanceFailedException(null, $violations);
        }
    }
}
