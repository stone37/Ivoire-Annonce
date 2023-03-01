<?php

namespace App\Event;

use App\Entity\User;

class NotificationReadEvent
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}