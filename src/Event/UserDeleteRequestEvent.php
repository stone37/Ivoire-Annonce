<?php

namespace App\Event;

use App\Entity\User;

class UserDeleteRequestEvent
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
