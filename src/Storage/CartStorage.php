<?php

namespace App\Storage;

class CartStorage
{
    public function __construct(private SessionStorage $storage)
    {
    }

    public function get(): ?array
    {
        return $this->storage->get($this->provideKey());
    }

    public function has(): bool
    {
        return $this->storage->has($this->provideKey());
    }

    public function set(mixed $data): void
    {
        $this->storage->set($this->provideKey(), $data);
    }

    public function remove(): void
    {
        $this->storage->remove($this->provideKey());
    }

    public function init(): void
    {
        $this->remove();
    }

    private function provideKey(): string
    {
        return '_app_card';
    }
}
