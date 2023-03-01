<?php

namespace App\Storage;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CookieStorage implements StorageInterface, EventSubscriberInterface
{
    private ParameterBag $requestCookies;
    private ParameterBag $responseCookies;

    public function __construct()
    {
        $this->requestCookies = new ParameterBag();
        $this->responseCookies = new ParameterBag();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 1024]],
            KernelEvents::RESPONSE => [['onKernelResponse', -1024]],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->requestCookies = new ParameterBag($event->getRequest()->cookies->all());
        $this->responseCookies = new ParameterBag();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        foreach ($this->responseCookies as $name => $value) {
            $response->headers->setCookie(new Cookie((string) $name, $value));
        }

        $this->requestCookies = new ParameterBag();
        $this->responseCookies = new ParameterBag();
    }

    public function has(string $name): bool
    {
        return !in_array($this->get($name), ['', null], true);
    }

    public function get(string $name, $default = null)
    {
        return $this->responseCookies->get($name, $this->requestCookies->get($name, $default));
    }

    public function set(string $name, $value): void
    {
        $this->responseCookies->set($name, $value);
    }

    public function remove(string $name): void
    {
        $this->set($name, null);
    }

    public function all(): array
    {
        return array_merge($this->responseCookies->all(), $this->requestCookies->all());
    }
}

