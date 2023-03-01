<?php

namespace App\Model;

class AutosSearch extends AdvertSearch
{
    private ?string $marque = '';

    private ?string $model = '';

    private ?array $autoYear = [];

    private ?array $kilo = [];

    private ?string $typeCarburant = '';

    private ?string $autoState = '';

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getAutoYear(): ?array
    {
        return $this->autoYear;
    }

    public function setAutoYear(?array $autoYear): self
    {
        $this->autoYear = $autoYear;

        return $this;
    }

    public function getKilo(): ?array
    {
        return $this->kilo;
    }

    public function setKilo(?array $kilo): self
    {
        $this->kilo = $kilo;

        return $this;
    }

    public function getTypeCarburant(): ?string
    {
        return $this->typeCarburant;
    }

    public function setTypeCarburant(?string $typeCarburant): self
    {
        $this->typeCarburant = $typeCarburant;

        return $this;
    }

    public function getAutoState(): ?string
    {
        return $this->autoState;
    }

    public function setAutoState(?string $autoState): self
    {
        $this->autoState = $autoState;

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
            'price' => $this->getPrice(),
            'marque' => $this->getMarque(),
            'model' => $this->getModel(),
            'autoYear' => $this->getAutoYear(),
            'kilo' => $this->getKilo(),
            'typeCarburant' => $this->getTypeCarburant(),
            'autoState' => $this->getAutoState()
        ];
    }
}
