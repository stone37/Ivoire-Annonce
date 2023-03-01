<?php

namespace App\Twig;

use App\Util\CurrencyUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CurrencyExtension extends AbstractExtension
{
    public function __construct(private CurrencyUtil $util)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('app_currency_symbol', [$this->util, 'convertCurrencyCodeToSymbol']),
        ];
    }
}