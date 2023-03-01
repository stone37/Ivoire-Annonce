<?php

namespace App\EventListener;

use App\Context\StorageBasedLocaleContext;
use App\Provider\LocaleProvider;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestLocaleSetter implements EventSubscriberInterface
{
    public function __construct(
        private LocaleProvider $localeProvider,
        private StorageBasedLocaleContext $localeContext
    )
    {
    }

    #[ArrayShape([KernelEvents::REQUEST => "string"])] public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $request->setLocale($this->localeContext->getLocaleCode());
        $request->setDefaultLocale($this->localeProvider->getDefaultLocaleCode());
    }
}
