<?php

namespace App\PropertyNameResolver;

class NameResolver
{
    private string $propertyPrefix;

    public function __construct(string $propertyPrefix)
    {
        $this->propertyPrefix = $propertyPrefix;
    }

    public function resolvePropertyName(): string
    {
        return strtolower($this->propertyPrefix);
    }
}
