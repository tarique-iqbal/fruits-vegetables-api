<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationHelper implements ValidationHelperInterface
{
    private array $messages = [];

    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    public function validate($entity): bool
    {
        $errors = $this->validator->validate($entity);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->messages[] = [
                    'property' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }

            return false;
        }

        return true;
    }

    public function getErrorMessages(): array
    {
        return $this->messages;
    }
}
