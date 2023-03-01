<?php

namespace App\Model;

class ImmobilierSearch extends AdvertSearch
{
    private ?array $surface = [];

    private ?string $nombrePiece = '';

    private ?string $immobilierState = '';

    private ?string $nombreChambre = '';

    private ?string $nombreSalleBain = '';

    private ?array $access = [];

    private ?array $proximite = [];

    private ?array $interior = [];

    private ?array $exterior = [];

    public function getSurface(): ?array
    {
        return $this->surface;
    }

    public function setSurface(?array $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getNombrePiece(): ?string
    {
        return $this->nombrePiece;
    }

    public function setNombrePiece(?string $nombrePiece): self
    {
        $this->nombrePiece = $nombrePiece;

        return $this;
    }

    public function getImmobilierState(): ?string
    {
        return $this->immobilierState;
    }

    public function setImmobilierState(?string $immobilierState): self
    {
        $this->immobilierState = $immobilierState;

        return $this;
    }

    public function getNombreChambre(): ?string
    {
        return $this->nombreChambre;
    }

    public function setNombreChambre(?string $nombreChambre): self
    {
        $this->nombreChambre = $nombreChambre;

        return $this;
    }

    public function getNombreSalleBain(): ?string
    {
        return $this->nombreSalleBain;
    }

    public function setNombreSalleBain(?string $nombreSalleBain): self
    {
        $this->nombreSalleBain = $nombreSalleBain;

        return $this;
    }

    public function getAccess(): ?array
    {
        return $this->access;
    }

    public function setAccess(?array $access): self
    {
        $this->access = $access;

        return $this;
    }

    public function getProximite(): ?array
    {
        return $this->proximite;
    }

    public function setProximite(?array $proximite): self
    {
        $this->proximite = $proximite;

        return $this;
    }

    public function getInterior(): ?array
    {
        return $this->interior;
    }

    public function setInterior(?array $interior): self
    {
        $this->interior = $interior;

        return $this;
    }

    public function getExterior(): ?array
    {
        return $this->exterior;
    }

    public function setExterior(?array $exterior): self
    {
        $this->exterior = $exterior;

        return $this;
    }
}
