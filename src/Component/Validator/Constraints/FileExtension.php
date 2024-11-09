<?php

declare(strict_types=1);

namespace App\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class FileExtension extends Constraint
{
    public string $message = 'Expected file extension to be {{ extension }}, received: {{ received }}';

    public array $allowedExtensions;

    public function __construct(
        array $allowedExtensions,
        ?string $message = null,
        ?array $groups = null,
        $payload = null,
    ) {
        parent::__construct([], $groups, $payload);

        $this->allowedExtensions = $allowedExtensions;
        $this->message = $message ?? $this->message;
    }
}
