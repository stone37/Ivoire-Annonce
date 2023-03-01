<?php

namespace App\Util;

use Symfony\Component\Intl\Currencies;

class CurrencyUtil
{
    public function convertCurrencyCodeToSymbol(string $code): string
    {
        return Currencies::getSymbol($code);
    }
}