<?php

namespace App\Event;

use App\Entity\PasswordResetToken;
use App\Entity\User;
use JetBrains\PhpStorm\Pure;

final class PasswordResetTokenCreatedEvent
{
    public function __construct(private PasswordResetToken $token)
    {
    }

    #[Pure] public function getUser(): User
    {
        return $this->token->getOwner();
    }

    public function getToken(): PasswordResetToken
    {
        return $this->token;
    }
}
