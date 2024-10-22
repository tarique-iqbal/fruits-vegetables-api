<?php

declare(strict_types=1);

namespace App\Dto\Response;

use Symfony\Component\Serializer\Annotation\Groups;

class VegetableDto
{
    #[Groups(['gram', 'kilogram'])]
    private int $id;

    #[Groups(['gram', 'kilogram'])]
    private string $name;

    #[Groups(['gram', 'kilogram'])]
    private string $alias;

    #[Groups(['gram'])]
    private int $gram;

    #[Groups(['kilogram'])]
    private float $kilogram;

    #[Groups(['gram', 'kilogram'])]
    private \DateTimeInterface $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

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

    public function getGram(): int
    {
        return $this->gram;
    }

    public function setGram(int $gram): static
    {
        $this->gram = $gram;

        return $this;
    }

    public function getKilogram(): float
    {
        return $this->kilogram;
    }

    public function setKilogram(float $kilogram): static
    {
        $this->kilogram = $kilogram;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
