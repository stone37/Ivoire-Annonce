<?php

namespace App\Converter;

use InvalidArgumentException;
use Symfony\Component\Intl\Currencies;

class CurrencyNameConverter
{
    public function convertToCode(string $name, ?string $locale = null): string
    {
        $names = Currencies::getNames($locale ?? 'fr');
        $currencyCode = array_search($name, $names, true);

        if (false === $currencyCode) {
            throw new InvalidArgumentException(sprintf(
                'Currency "%s" not found! Available names: %s.',
                $name,
                implode(', ', $names)
            ));
        }

        return $currencyCode;
    }
}