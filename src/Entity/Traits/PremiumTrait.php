<?php

namespace App\Entity\Traits;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait PremiumTrait
{
    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $premiumEnd = null;

    public function isPremium(): bool
    {
        return $this->premiumEnd > new DateTime();
    }

    public function getPremiumEnd(): ?DateTimeImmutable
    {
        return $this->premiumEnd;
    }

    public function setPremiumEnd(?DateTimeImmutable $premiumEnd): self
    {
        $this->premiumEnd = $premiumEnd;

        return $this;
    }
}

