<?php

namespace App\Service;

use App\Entity\Thread;
use App\Entity\User;
use App\Event\ThreadDeleteEvent;
use App\Event\ThreadUnDeleteEvent;
use App\Security\ThreadAuthorizer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ThreadDeleter
{
    public function __construct(
        private ThreadAuthorizer $authorizer,
        private Security $security,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function markAsDeleted(Thread $thread)
    {
        if (!$this->authorizer->canDeleteThread($thread)) {
            throw new AccessDeniedException('You are not allowed to delete this thread');
        }
        $thread->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), true);

        $this->dispatcher->dispatch(new ThreadDeleteEvent($thread));
    }

    public function markAsUndeleted(Thread $thread)
    {
        if (!$this->authorizer->canDeleteThread($thread)) {
            throw new AccessDeniedException('You are not allowed to delete this thread');
        }
        $thread->setIsDeletedByParticipant($this->getAuthenticatedParticipant(), false);

        $this->dispatcher->dispatch(new ThreadUnDeleteEvent($thread));
    }

    /**
     * Gets the current authenticated user.
     */
    private function getAuthenticatedParticipant(): UserInterface|User
    {
        return $this->security->getUser();
    }
}