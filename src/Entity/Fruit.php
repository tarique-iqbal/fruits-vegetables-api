<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FruitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('alias')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: FruitRepository::class)]
class Fruit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private int $id;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 64,
        minMessage: 'Fruit name must be at least {{ limit }} characters long.',
        maxMessage: 'Fruit name can not be longer than {{ limit }} characters.',
    )]
    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 64, unique: true)]
    private string $alias;

    #[Assert\NotNull]
    #[Assert\NotBlank]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    private int $gram;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['notnull' => true])]
    private \DateTimeInterface $dateTimeAdded;

    public function getId(): int
    {
        return $this->id;
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

    public function getDateTimeAdded(): \DateTimeInterface
    {
        return $this->dateTimeAdded;
    }

    #[ORM\PrePersist]
    public function setDateTimeAddedValue(): void
    {
        $this->dateTimeAdded = new \DateTimeImmutable();
    }
}
