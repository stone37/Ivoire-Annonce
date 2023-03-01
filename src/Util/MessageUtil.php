<?php

namespace App\Util;

use App\Entity\Thread;
use App\Provider\ThreadProvider;
use App\Security\ThreadAuthorizer;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageUtil
{
    private ?int $nbUnreadMessagesCache = null;

    public function __construct(
        private Security $security,
        private ThreadProvider $provider,
        private ThreadAuthorizer $authorizer
    )
    {
    }

    public function isRead(Thread $thread): bool
    {
        return $thread->isReadByParticipant($this->getAuthenticatedParticipant());
    }

    public function canDeleteThread(Thread $thread): bool
    {
        return $this->authorizer->canDeleteThread($thread);
    }

    public function isThreadDeletedByParticipant(Thread $thread): bool
    {
        return $thread->isDeletedByParticipant($this->getAuthenticatedParticipant());
    }

    public function getNbUnread(): int
    {
        if (null === $this->nbUnreadMessagesCache) {
            $this->nbUnreadMessagesCache = $this->provider->getNbUnreadMessages();
        }

        return $this->nbUnreadMessagesCache;
    }

    private function getAuthenticatedParticipant(): ?UserInterface
    {
        return $this->security->getUser();
    }
}
