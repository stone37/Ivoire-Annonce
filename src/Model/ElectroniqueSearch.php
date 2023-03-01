<?php

namespace App\Model;

class ElectroniqueSearch extends AdvertSearch
{
    private ?string $state = '';

    private ?string $brand = '';

    private ?array $processing = [];

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getProcessing(): ?array
    {
        return $this->processing;
    }

    public function setProcessing(?array $processing): self
    {
        $this->processing = $processing;

        return $this;
    }
}