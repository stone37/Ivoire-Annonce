<?php

namespace App\Util;

use App\Entity\Settings;
use App\Manager\SettingsManager;
use App\Repository\CurrencyRepository;
use NumberFormatter;
use Symfony\Component\Intl\Currencies;

class LocaleCurrencyUtil
{
    private ?Settings $settings;
    
    public function __construct(
        private CurrencyRepository $repository,
        SettingsManager $manager
    )
    {
        $this->settings = $manager->get();
    }

    public function getCurrencyCode(string $locale): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $currency = $formatter->getTextAttribute(NumberFormatter::CURRENCY_CODE);

        if (in_array($currency, $this->repository->getCodes())) {
            return $currency;
        }

        return $this->settings->getBaseCurrency()->getCode();
    }

    public function getCurrencySymbol(string $locale): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $currency = $formatter->getTextAttribute(NumberFormatter::CURRENCY_CODE);

        if (in_array($currency, $this->repository->getCodes())) {
            return Currencies::getSymbol($currency);
        }

        return Currencies::getSymbol($this->settings->getBaseCurrency()->getCode());
    }
}