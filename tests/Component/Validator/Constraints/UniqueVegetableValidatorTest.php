<?php

declare(strict_types=1);

namespace App\Tests\Component\Validator\Constraints;

use App\Component\Validator\Constraints\UniqueVegetable;
use App\Component\Validator\Constraints\UniqueVegetableValidator;
use App\Repository\VegetableRepository;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class UniqueVegetableValidatorTest extends ConstraintValidatorTestCase
{
    private VegetableRepository $vegetableRepository;

    protected function setUp(): void
    {
        $this->vegetableRepository = $this->createMock(VegetableRepository::class);

        parent::setUp();
    }

    protected function createValidator(): UniqueVegetableValidator
    {
        return new UniqueVegetableValidator($this->vegetableRepository);
    }

    public function testUniqueVegetable()
    {
        $this->vegetableRepository->method('existsByAlias')->willReturn(false);

        $this->validator->validate('unique-alias', new UniqueVegetable());

        $this->assertNoViolation();
    }

    public function testNonUniqueVegetable()
    {
        $this->vegetableRepository->method('existsByAlias')->willReturn(true);

        $constraint = new UniqueVegetable();
        $this->validator->validate('existing-alias', $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', 'existing-alias')
            ->setCode(UniqueVegetable::NOT_UNIQUE_ERROR)
            ->assertRaised();
    }

    public function testEmptyVegetable()
    {
        $this->validator->validate('', new UniqueVegetable());
        $this->assertNoViolation();
    }
}
