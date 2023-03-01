<?php

namespace App\Security;

use App\Entity\Advert;
use App\Entity\User;
use JetBrains\PhpStorm\Pure;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdvertVoter extends Voter
{
    private const CREATE = 'created';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE])) {
            return false;
        }

        if (!$subject instanceof Advert) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!$subject instanceof Advert) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($subject, $user);
        }

        throw new LogicException('This code should not be reached!');
    }

    #[Pure] private function canCreate(Advert $advert, User $user): bool
    {
        return $user === $advert->getOwner();
    }
}
