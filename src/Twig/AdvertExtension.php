<?php

namespace App\Twig;

use App\Util\AdvertUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AdvertExtension extends AbstractExtension
{
    public function __construct(private AdvertUtil $util)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('app_advert_type', [$this->util, 'getTypeData']),
            new TwigFilter('app_advert_processing', [$this->util, 'getProcessingData'])
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('app_advert_image_principale', [$this->util, 'getImagePrincipale']),
        ];
    }
}
