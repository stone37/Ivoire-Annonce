<?php

namespace App\Twig;

use App\Util\OptionUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class OptionExtension extends AbstractExtension
{
    public function __construct(private OptionUtil $util)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_advert_option_data', [$this->util, 'getData']),
            new TwigFunction('app_has_option', [$this->util, 'verify']),
            new TwigFunction('app_product_name', [$this->util, 'getName']),
        ];
    }
}
