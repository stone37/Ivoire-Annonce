<?php

namespace App\Service;

use App\Storage\LocaleStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class StorageBasedLocaleSwitcher
{
    private LocaleStorage $localeStorage;

    public function __construct(LocaleStorage $localeStorage)
    {
        $this->localeStorage = $localeStorage;
    }

    public function handle(Request $request, string $localeCode): RedirectResponse
    {
        $this->localeStorage->set($localeCode);

        return new RedirectResponse($request->headers->get('referer', $request->getSchemeAndHttpHost()));
    }
}
