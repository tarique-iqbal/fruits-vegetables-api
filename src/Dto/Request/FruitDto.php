<?php

declare(strict_types=1);

namespace App\Dto\Request;

use App\Component\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class FruitDto
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 64,
        minMessage: 'Fruit name must be at least {{ limit }} characters long.',
        maxMessage: 'Fruit name can not be longer than {{ limit }} characters.',
    )]
    #[Assert\Regex(
        pattern: "/[\^<,@\/\{\}\(\)\[\]\!\&\\\`\'\~\*\$%\?=>:\|;#0-9\x22]+/i",
        message: "Special characters are not allowed in Fruit name.",
        match: false
    )]
    private string $name;

    #[Assert\NotBlank]
    #[AppAssert\UniqueFruit]
    private string $alias;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    private int $quantity;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: "/(g|kg)/",
        message: "Invalid unit provided.",
        match: true
    )]
    private string $unit;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): static
    {
        $this->alias = $alias;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }
}
