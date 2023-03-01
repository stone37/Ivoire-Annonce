<?php

namespace App\Storage;

use Symfony\Component\HttpFoundation\RequestStack;

class SessionStorage implements StorageInterface
{
    public function __construct(private RequestStack $request)
    {
    }

    public function has(string $name): bool
    {
        return $this->request->getSession()->has($name);
    }

    public function get(string $name, $default = null)
    {
        return $this->request->getSession()->get($name, $default);
    }

    public function set(string $name, $value): void
    {
        $this->request->getSession()->set($name, $value);
    }

    public function remove(string $name): void
    {
        $this->request->getSession()->remove($name);
    }

    public function all(): array
    {
        return $this->request->getSession()->all();
    }
}