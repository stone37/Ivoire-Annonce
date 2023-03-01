<?php

namespace App\Twig;

use App\Util\UnsetArrayElementsUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UnsetArrayElementsExtension extends AbstractExtension
{
    public function __construct(private UnsetArrayElementsUtil $util)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('unset_elements', [$this->util, 'unsetElements']),
        ];
    }
}
