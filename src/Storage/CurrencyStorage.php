<?php

namespace App\Storage;

use App\Entity\Currency;
use App\Entity\Settings;
use App\Repository\CurrencyRepository;

class CurrencyStorage
{
    public function __construct(
        private CookieStorage $storage,
        private CurrencyRepository $currencyRepository
    )
    {
    }

    public function set(Settings $settings, string $currencyCode): void
    {
        if ($this->isBaseCurrency($currencyCode, $settings) || !$this->isAvailableCurrency($currencyCode)) {
            $this->storage->remove($this->provideKey());

            return;
        }

        $this->storage->set($this->provideKey(), $currencyCode);
    }

    public function get(): ?string
    {
        return $this->storage->get($this->provideKey());
    }

    private function isBaseCurrency(string $currencyCode, Settings $settings): bool
    {
        return $currencyCode === $settings->getBaseCurrency()->getCode();
    }

    private function isAvailableCurrency(string $currencyCode): bool
    {
        $availableCurrencies = array_map(
            function (Currency $currency) {
                return $currency->getCode();
            },
            $this->currencyRepository->findAll()
        );

        return in_array($currencyCode, $availableCurrencies, true);
    }

    private function provideKey(): string
    {
        return '_app_currency';
    }
}
