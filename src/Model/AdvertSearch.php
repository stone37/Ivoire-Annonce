<?php

namespace App\Model;

abstract class AdvertSearch
{
    private ?string $category = '';

    private ?string $subCategory = '';

    private ?int $type = null;

    private ?string $city = '';

    private ?bool $urgent = false;

    private ?array $price = [];

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSubCategory(): ?string
    {
        return $this->subCategory;
    }

    public function setSubCategory(?string $subCategory): self
    {
        $this->subCategory = $subCategory;

        return $this;
    }

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

    public function toArray(): array
    {
        return [
            'category' => $this->getCategory(),
            'subCategory' => $this->getSubCategory(),
            'type' => $this->getType(),
            'city' => $this->getCity(),
            'urgent' => $this->getUrgent(),
            'price' => $this->getPrice()
        ];
    }
}
