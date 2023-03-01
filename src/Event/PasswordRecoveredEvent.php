<?php

namespace App\Event;

use App\Entity\User;

final class PasswordRecoveredEvent
{
    public function __construct(private User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
