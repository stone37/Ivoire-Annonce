<?php

namespace App\Exception;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Erreur renvoyée lorsque l'on ne trouve pas d'utilisateur correspondant à la réponse de l'OAUTH.
 */
class UserOauthNotFoundException extends AuthenticationException
{
    public function __construct(private ResourceOwnerInterface $resourceOwner)
    {
        parent::__construct();
    }

    public function getResourceOwner(): ResourceOwnerInterface
    {
        return $this->resourceOwner;
    }
}

