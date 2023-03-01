<?php

namespace App\Exception;

use App\Entity\User;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * L'utilisateur est déjà authentifié.
 */
class UserAuthenticatedException extends AuthenticationException
{
    public function __construct(private User $user, private ResourceOwnerInterface $resourceOwner)
    {
        parent::__construct();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getResourceOwner(): ResourceOwnerInterface
    {
        return $this->resourceOwner;
    }
}

