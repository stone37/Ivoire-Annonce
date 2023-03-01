<?php

namespace App\PropertyNameResolver;

class YearNameResolver
{
    public function __construct(private string $yearPropertyPrefix)
    {
    }

    public function resolveMinYearName(): string
    {
        return 'min_' . $this->yearPropertyPrefix;
    }

    public function resolveMaxYearName(): string
    {
        return 'max_' . $this->yearPropertyPrefix;
    }
}