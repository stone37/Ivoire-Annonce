<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\DiscountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRepository::class)]
class Discount
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $discount = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $utilisation = null;

    #[ORM\Column(nullable: true)]
    private ?int $utiliser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): self
    {
        $this->discount = $discount;

        return $this;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUtilisation(): ?int
    {
        return $this->utilisation;
    }

    public function setUtilisation(?int $utilisation): self
    {
        $this->utilisation = $utilisation;

        return $this;
    }

    public function getUtiliser(): ?int
    {
        return $this->utiliser;
    }

    public function setUtiliser(?int $utiliser): self
    {
        $this->utiliser = $utiliser;

        return $this;
    }
}
