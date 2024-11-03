<?php

declare(strict_types=1);

namespace App\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class FileExists extends Constraint
{
    public string $message = 'The file {{ file }} does not exist.';

    public function __construct(?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
