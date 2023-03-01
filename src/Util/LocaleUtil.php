<?php

namespace App\Util;

use App\Context\ImmutableLocaleContext;
use App\Converter\LocaleConverter;
use App\Exception\LocaleNotFoundException;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;

class LocaleUtil
{
    public function __construct(
        private LocaleConverter $localeConverter,
        private ?ImmutableLocaleContext $localeContext
    )
    {
    }

    public function convertCodeToName(string $code, ?string $localeCode = null): ?string
    {
        try {
            return $this->localeConverter->convertCodeToName($code, $this->getLocaleCode($localeCode));
        } catch (InvalidArgumentException) {
            return $code;
        }
    }

    #[Pure] private function getLocaleCode(?string $localeCode): ?string
    {
        if (null !== $localeCode) {
            return $localeCode;
        }

        if (null === $this->localeContext) {
            return null;
        }

        try {
            return $this->localeContext->getLocaleCode();
        } catch (LocaleNotFoundException) {
            return null;
        }
    }
}