<?php

namespace App\Context;

use App\Entity\Settings;
use App\Exception\CurrencyNotFoundException;
use App\Storage\CurrencyStorage;

class StorageBasedCurrencyContext
{
    private CurrencyStorage $currencyStorage;

    public function __construct(CurrencyStorage $currencyStorage)
    {
        $this->currencyStorage = $currencyStorage;
    }

    public function getCurrencyCode(?Settings $settings): ?string
    {
        if (!$settings) {
            return null;
        }

        $currencyCode = $this->currencyStorage->get();

        if (null === $currencyCode) {
            $currencyCode = $settings->getBaseCurrency();

            if (null === $currencyCode) {
                throw new CurrencyNotFoundException();
            }
        }

        return $currencyCode;
    }
}
