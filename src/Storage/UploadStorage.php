<?php

namespace App\Storage;

use App\Entity\Commande;
use App\Repository\CommandeRepository;

class UploadStorage
{
    public function __construct(private SessionStorage $storage)
    {
    }

    public function set(string $orderId): void
    {
        $this->storage->set($this->provideKey(), $orderId);
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
        return '_app_advert_images';
    }
}

