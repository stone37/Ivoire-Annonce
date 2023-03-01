<?php

namespace App\Twig;

use App\Util\LocaleCurrencyUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocaleCurrencyExtension extends AbstractExtension
{
    public function __construct(private LocaleCurrencyUtil $util)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_locale_currency_code', [$this->util, 'getCurrencyCode']),
            new TwigFunction('app_locale_currency_symbol', [$this->util, 'getCurrencySymbol'])
        ];
    }
}