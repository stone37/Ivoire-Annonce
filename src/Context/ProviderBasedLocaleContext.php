<?php

namespace App\Context;

use App\Exception\LocaleNotFoundException;
use App\Provider\LocaleProvider;

class ProviderBasedLocaleContext
{
    public function __construct(private LocaleProvider $localeProvider)
    {
    }

    public function getLocaleCode(): string
    {
        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();
        $localeCode = $this->localeProvider->getDefaultLocaleCode();

        if (!in_array($localeCode, $availableLocalesCodes, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}