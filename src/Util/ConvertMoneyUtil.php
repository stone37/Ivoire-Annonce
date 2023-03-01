<?php

namespace App\Util;

use App\Converter\CurrencyConverter;

class ConvertMoneyUtil
{
    public function __construct(private CurrencyConverter $converter)
    {
    }

    public function convertAmount(int $amount, ?string $sourceCurrencyCode, ?string $targetCurrencyCode): string
    {
        return (string) $this->converter->convert($amount, $sourceCurrencyCode, $targetCurrencyCode);
    }
}
