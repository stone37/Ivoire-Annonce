<?php

namespace App\Security;

use App\Entity\Thread;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ThreadAuthorizer
{
    public function __construct(private Security $security)
    {
    }

    public function canSeeThread(Thread $thread)
    {
        return $this->getAuthenticatedParticipant() && $thread->isParticipant($this->getAuthenticatedParticipant());
    }

    public function canDeleteThread(Thread $thread): bool
    {
        return $this->canSeeThread($thread);
    }

    public function canMessageParticipant(User $user): bool
    {
        return true;
    }

    private function getAuthenticatedParticipant(): UserInterface|User
    {
        return $this->security->getUser();
    }
}