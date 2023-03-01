<?php

namespace App\PropertyNameResolver;

class KiloNameResolver
{
    public function __construct(private string $kiloPropertyPrefix)
    {
    }

    public function resolveMinKiloName(): string
    {
        return 'min_' . $this->kiloPropertyPrefix;
    }

    public function resolveMaxKiloName(): string
    {
        return 'max_' . $this->kiloPropertyPrefix;
    }
}