<?php

namespace App\Event;

use App\Entity\User;

class UserCreatedEvent
{
    public function __construct(private User $user, private bool $usingOauth = false)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isUsingOauth(): bool
    {
        return $this->usingOauth;
    }
}

