<?php

namespace App\PropertyNameResolver;

class PriceNameResolver
{
    public function __construct(private string $pricePropertyPrefix)
    {
    }

    public function resolveMinPriceName(): string
    {
        return 'min_' . $this->pricePropertyPrefix;
    }

    public function resolveMaxPriceName(): string
    {
        return 'max_' . $this->pricePropertyPrefix;
    }
}