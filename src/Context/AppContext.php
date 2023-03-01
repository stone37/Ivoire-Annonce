<?php

namespace App\Context;

use App\Entity\Settings;
use App\Entity\User;
use App\Manager\SettingsManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AppContext
{
    private ?Settings $settings;

    public function __construct(
        SettingsManager $manager,
        private StorageBasedCurrencyContext $currencyContext,
        private StorageBasedLocaleContext $localeContext,
        private Security $security
    )
    {
        $this->settings = $manager->get();
    }

    public function getSettings(): ?Settings
    {
        return $this->settings;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyContext->getCurrencyCode($this->settings);
    }

    public function getLocaleCode(): string
    {
        return $this->localeContext->getLocaleCode();
    }

    /**
     * @return User|UserInterface
     */
    public function getUser(): ?User
    {
        return $this->security->getUser();
    }
}

