<?php

namespace App\Converter;

use InvalidArgumentException;
use Symfony\Component\Intl\Exception\MissingResourceException;
use Symfony\Component\Intl\Locales;
use Webmozart\Assert\Assert;

class LocaleConverter
{
    public function convertNameToCode(string $name, ?string $locale = null): string
    {
        $names = Locales::getNames($locale ?? 'fr');
        $code = array_search($name, $names, true);

        Assert::string($code, sprintf('Cannot find code for "%s" locale name', $name));

        return $code;
    }

    public function convertCodeToName(string $code, ?string $locale = null): string
    {
        try {
            return Locales::getName($code, $locale ?? 'en');
        } catch (MissingResourceException $e) {
            throw new InvalidArgumentException(sprintf('Cannot find name for "%s" locale code', $code), 0, $e);
        }
    }
}