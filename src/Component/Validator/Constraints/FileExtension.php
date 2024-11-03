<?php

declare(strict_types=1);

namespace App\Component\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class FileExtension extends Constraint
{
    public string $message = 'Expected file extension to be {{ extension }}, received: {{ received }}';

    public string $extension;

    public function __construct(
        string $extension,
        ?string $message = null,
        ?array $groups = null,
        $payload = null,
    ) {
        parent::__construct([], $groups, $payload);

        $this->extension = $extension;
        $this->message = $message ?? $this->message;
    }
}
