<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Clock\DatePoint;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numCode = null;

    #[ORM\Column(length: 255)]
    private ?string $charCode = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $nominal = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $value = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 4)]
    private ?string $vunitRate = null;

    #[ORM\Column(type: 'date_point')]
    private ?DatePoint $date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNumCode(): ?string
    {
        return $this->numCode;
    }

    public function setNumCode(string $numCode): static
    {
        $this->numCode = $numCode;

        return $this;
    }

    public function getCharCode(): ?string
    {
        return $this->charCode;
    }

    public function setCharCode(string $charCode): static
    {
        $this->charCode = $charCode;

        return $this;
    }

    public function getNominal(): ?int
    {
        return $this->nominal;
    }

    public function setNominal(int $nominal): static
    {
        $this->nominal = $nominal;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getVunitRate(): ?string
    {
        return $this->vunitRate;
    }

    public function setVunitRate(string $vunitRate): static
    {
        $this->vunitRate = $vunitRate;

        return $this;
    }

    public function getDate(): ?DatePoint
    {
        return $this->date;
    }

    public function setDate(DatePoint $date): static
    {
        $this->date = $date;

        return $this;
    }
}
