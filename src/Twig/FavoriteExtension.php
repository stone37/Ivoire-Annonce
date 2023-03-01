<?php

namespace App\Twig;

use App\Util\FavoriteUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FavoriteExtension extends AbstractExtension
{
    public function __construct(private FavoriteUtil $util)
    {
    }

    public function getFunctions()
    {
        return [new TwigFunction('app_has_favoris', [$this->util, 'verify'])];
    }
}
