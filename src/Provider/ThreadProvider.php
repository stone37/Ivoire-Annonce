<?php

namespace App\Provider;

use App\Entity\Thread;
use App\Entity\User;
use App\Manager\MessageManager;
use App\Manager\ThreadManager;
use App\Security\ThreadAuthorizer;
use App\Service\Reader;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ThreadProvider
{
    public function __construct(
        private ThreadManager $manager,
        private MessageManager $messageManager,
        private Security $security,
        private Reader $threadReader,
        private ThreadAuthorizer $authorizer
    )
    {
    }

    public function getThreads(): array
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->manager->findParticipantThreads($participant);
    }

    public function getInboxThreads(): array
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->manager->findParticipantInboxThreads($participant);
    }

    public function getSentThreads(): array
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->manager->findParticipantSentThreads($participant);
    }

    public function getDeletedThreads(): array
    {
        $participant = $this->getAuthenticatedParticipant();

        return $this->manager->findParticipantDeletedThreads($participant);
    }

    public function getThread(int $threadId): Thread
    {
        $thread = $this->manager->findThreadById($threadId);

        if (!$thread) {
            throw new NotFoundHttpException('There is no such thread');
        }

        if (!$this->authorizer->canSeeThread($thread)) {
            throw new AccessDeniedException('You are not allowed to see this thread');
        }

        // Load the thread messages before marking them as read
        // because we want to see the unread messages

        $this->threadReader->markAsRead($thread);

        return $thread;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getNbUnreadMessages(): int
    {
        return $this->messageManager->getNbUnreadMessageByParticipant($this->getAuthenticatedParticipant());
    }

    private function getAuthenticatedParticipant(): UserInterface|User
    {
        return $this->security->getUser();
    }
}
