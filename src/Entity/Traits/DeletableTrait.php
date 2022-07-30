<?php

namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait DeletableTrait
{
    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $deleteAt = null;

    public function getDeleteAt(): ?DateTimeImmutable
    {
        return $this->deleteAt;
    }

    public function setDeleteAt(?DateTimeImmutable $deleteAt): self
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }
}

