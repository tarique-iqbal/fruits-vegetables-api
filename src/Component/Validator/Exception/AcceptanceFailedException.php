<?php

declare(strict_types=0);

namespace App\Component\Validator\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\RuntimeException;

class AcceptanceFailedException extends RuntimeException
{
    private ConstraintViolationListInterface $violations;

    private mixed $value;

    public function __construct(mixed $value, ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
        $this->value = $value;
        parent::__construct($violations);
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
