<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\LocaleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Intl\Locales;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['code'], message: 'Le code régional doit être unique.')]
#[ORM\Entity(repositoryClass: LocaleRepository::class)]
class Locale
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez choisir le code régional.')]
    #[Assert\Regex(pattern: '/^[\w-]*$/', message: 'Le code régional peut uniquement être constitué de lettres, chiffres, tirets et tirets bas.')]
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

    public function getName(?string $locale = null): ?string
    {
        return Locales::getName($this->getCode(), $locale);
    }

    public function __toString(): string
    {
        return (string) $this->getName();
    }
}
