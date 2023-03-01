<?php

namespace App\Service;

use App\Entity\Thread;
use App\Entity\User;
use App\Manager\ThreadManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class Reader
{
    public function __construct(
        private Security $security,
        private ThreadManager $manager
    )
    {
    }

    public function markAsRead(Thread $thread): void
    {
        $participant = $this->getAuthenticatedParticipant();

        if ($thread->isReadByParticipant($participant)) {
            return;
        }

        $this->manager->markAsReadByParticipant($thread, $participant);
    }

    public function markAsUnread(Thread $thread): void
    {
        $participant = $this->getAuthenticatedParticipant();

        if (!$thread->isReadByParticipant($participant)) {
            return;
        }

        $this->manager->markAsUnreadByParticipant($thread, $participant);
    }

    private function getAuthenticatedParticipant(): UserInterface|User
    {
        return $this->security->getUser();
    }
}