<?php

namespace App\Context;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImmutableLocaleContext
{
    private ?string $localeCode;

    public function __construct(ParameterBagInterface $parameter)
    {
        $this->localeCode = $parameter->get('locale');
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }
}