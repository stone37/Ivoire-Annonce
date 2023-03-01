<?php

namespace App\Storage;

interface StorageInterface
{
    public function has(string $name): bool;

    public function get(string $name, mixed $default = null);

    public function set(string $name, mixed $value): void;

    public function remove(string $name): void;

    public function all(): array;
}
