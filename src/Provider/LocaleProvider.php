<?php

namespace App\Provider;

use App\Entity\Locale;
use App\Repository\LocaleRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LocaleProvider
{
    private ?string $defaultLocaleCode;

    public function __construct(
        private LocaleRepository $localeRepository,
        ParameterBagInterface $parameter
    )
    {
        $this->defaultLocaleCode = $parameter->get('locale');
    }

    public function getAvailableLocalesCodes(): array
    {
        $locales = $this->localeRepository->findAll();

        return array_map(
            function (Locale $locale) {return (string) $locale->getCode();},
            $locales
        );
    }

    public function getDefaultLocaleCode(): string
    {
        return $this->defaultLocaleCode;
    }
}