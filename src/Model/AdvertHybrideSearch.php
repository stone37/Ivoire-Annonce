<?php

namespace App\Model;

class AdvertHybrideSearch
{
    private ?int $type = null;

    private ?string $city = '';

    private ?bool $urgent = false;

    private ?array $price = [];

    private ?string $data = null;

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getUrgent(): ?bool
    {
        return $this->urgent;
    }

    public function setUrgent(?bool $urgent): self
    {
        $this->urgent = $urgent;

        return $this;
    }

    public function getPrice(): ?array
    {
        return $this->price;
    }

    public function setPrice(?array $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'city' => $this->getCity(),
            'urgent' => $this->getUrgent(),
            'price' => $this->getPrice()
        ];
    }
}
