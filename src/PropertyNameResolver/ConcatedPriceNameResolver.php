<?php

namespace App\PropertyNameResolver;

class ConcatedPriceNameResolver
{
    private string $propertyPrefix;

    public function __construct(string $propertyPrefix)
    {
        $this->propertyPrefix = $propertyPrefix;
    }

    public function resolvePropertyName(string $suffix): string
    {
        return strtolower($this->propertyPrefix . '.' . $suffix);
    }
}
