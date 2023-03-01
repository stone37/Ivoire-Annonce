<?php

namespace App\PropertyNameResolver;

class SurfaceNameResolver
{
    public function __construct(private string $surfacePropertyPrefix)
    {
    }

    public function resolveMinSurfaceName(): string
    {
        return 'min_' . $this->surfacePropertyPrefix;
    }

    public function resolveMaxSurfaceName(): string
    {
        return 'max_' . $this->surfacePropertyPrefix;
    }
}