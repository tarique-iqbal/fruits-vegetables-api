<?php

declare(strict_types=1);

namespace App\Tests\Component\Validator\Constraints;

use App\Component\Validator\Constraints\FileExtension;
use App\Component\Validator\Constraints\FileExtensionValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class FileExtensionValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): FileExtensionValidator
    {
        return new FileExtensionValidator();
    }

    public function testValidExtension()
    {
        $constraint = new FileExtension(['json']);
        $value = 'document.json';

        $this->validator->validate($value, $constraint);
        $this->assertNoViolation();
    }

    public function testInvalidExtension()
    {
        $constraint = new FileExtension(['jpg', 'png']);
        $value = 'document.json';

        $this->validator->validate($value, $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ extension }}', implode(', ', $constraint->allowedExtensions))
            ->setParameter('{{ received }}', 'json')
            ->assertRaised();
    }
}
