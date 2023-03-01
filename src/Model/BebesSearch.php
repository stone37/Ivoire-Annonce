<?php

namespace App\Model;

class BebesSearch extends AdvertSearch
{
    private ?string $state = '';

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