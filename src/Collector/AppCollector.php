<?php

namespace App\Collector;

use App\Context\AppContext;
use App\Entity\Settings;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppCollector
{
    public function __construct(
        private AppContext $appContext,
        private ParameterBagInterface $parameter
    )
    {
    }

    public function getName(): ?string
    {
        return $this->getSettings() ? $this->getSettings()->getName() : null;
    }

    public function getDefaultLocaleCode(): ?string
    {
        return $this->parameter->get('locale');
    }

    public function getLocaleCode(): ?string
    {
        return $this->appContext->getLocaleCode();
    }

    public function getCurrencyCode(): ?string
    {
        return $this->appContext->getCurrencyCode();
    }

    #[Pure] public function getDefaultCurrencyCode(): ?string
    {
        return $this->getSettings() ? $this->getSettings()->getBaseCurrency()->getCode() : null;
    }

    #[Pure] public function getSettings(): ?Settings
    {
        return $this->appContext->getSettings();
    }
}