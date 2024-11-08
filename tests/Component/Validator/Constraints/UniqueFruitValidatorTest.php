<?php

declare(strict_types=1);

namespace App\Tests\Component\Validator\Constraints;

use App\Component\Validator\Constraints\UniqueFruit;
use App\Component\Validator\Constraints\UniqueFruitValidator;
use App\Repository\FruitRepository;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class UniqueFruitValidatorTest extends ConstraintValidatorTestCase
{
    private FruitRepository $fruitRepository;

    protected function setUp(): void
    {
        $this->fruitRepository = $this->createMock(FruitRepository::class);

        parent::setUp();
    }

    protected function createValidator(): UniqueFruitValidator
    {
        return new UniqueFruitValidator($this->fruitRepository);
    }

    public function testUniqueFruit()
    {
        $this->fruitRepository->method('existsByAlias')->willReturn(false);

        $this->validator->validate('unique-alias', new UniqueFruit());

        $this->assertNoViolation();
    }

    public function testNonUniqueFruit()
    {
        $this->fruitRepository->method('existsByAlias')->willReturn(true);

        $constraint = new UniqueFruit();
        $this->validator->validate('existing-alias', $constraint);

        $this->buildViolation($constraint->message)
            ->setParameter('{{ value }}', 'existing-alias')
            ->setCode(UniqueFruit::NOT_UNIQUE_ERROR)
            ->assertRaised();
    }

    public function testEmptyFruit()
    {
        $this->validator->validate('', new UniqueFruit());
        $this->assertNoViolation();
    }
}
