<?php

namespace App\Model\Admin;

use App\Entity\Category;

class AdvertSearch
{
    private ?string $advertType = null;

    private ?string $reference = null;

    private ?Category $category = null;

    private ?Category $subCategory = null;

    private ?string $city = null;

    public function getAdvertType(): ?string
    {
        return $this->advertType;
    }

    public function setAdvertType(?string $advertType): self
    {
        $this->advertType = $advertType;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSubCategory(): ?Category
    {
        return $this->subCategory;
    }

    public function setSubCategory(?Category $subCategory): self
    {
        $this->subCategory = $subCategory;

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
}

