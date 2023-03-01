<?php

namespace App\Twig;

use App\Util\CityUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CityExtension extends AbstractExtension
{
    public function __construct(private CityUtil $util)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_city_find', [$this->util, 'getCity'])
        ];
    }
}
