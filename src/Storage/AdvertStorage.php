<?php

namespace App\Storage;


class AdvertStorage
{
    public function __construct(private SessionStorage $storage)
    {
    }

    public function set(int $id): void
    {
        $this->storage->set($this->provideKey(), $id);
    }

    public function remove(): void
    {
        $this->storage->remove($this->provideKey());
    }

    public function get(): string
    {
        return $this->storage->get($this->provideKey());
    }

    public function has(): bool
    {
        return $this->storage->has($this->provideKey());
    }

    private function provideKey(): string
    {
        return '_app_advert';
    }
}

