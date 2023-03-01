<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['code'], message: 'Le code de la devise doit être unique.')]
#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez choisir le code de la devise.')]
    #[Assert\Length(min: 3, max: 3, exactMessage: 'Le code de la devise doit comporter exactement 3 caractères.')]
    #[Assert\Regex(pattern: '/^[\w-]*$/', message: 'Le code de la devise peut uniquement être constitué de lettres, chiffres, tirets et de tirets bas.')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getName(): ?string
    {
        if (null === $code = $this->getCode()) {
            return null;
        }

        return Currencies::getName($code);
    }

    #[Pure] public function __toString(): string
    {
        return (string) $this->getCode();
    }
}
