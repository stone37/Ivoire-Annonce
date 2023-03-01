<?php

namespace App\Manager;

use App\Entity\Advert;
use App\Entity\Thread;
use App\Entity\ThreadMetadata;
use App\Entity\User;
use App\Repository\ThreadRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;

class ThreadManager
{
    public function __construct(
        private ThreadRepository $repository,
        private MessageManager $messageManager
    )
    {
    }

    #[Pure] public function createThread(): Thread
    {
        return new Thread();
    }

    public function findThreadById(int $id): ?Thread
    {
        return $this->repository->find($id);
    }

    public function findParticipantThreads(User $user): array
    {
        return $this->repository->getParticipantThreads($user);
    }

    public function findParticipantInboxThreads(User $user): array
    {
        return $this->repository->getParticipantInboxThreads($user);
    }

    public function findParticipantSentThreads(User $user): array
    {
        return $this->repository->getParticipantSentThreads($user);
    }

    public function findParticipantDeletedThreads(User $user): array
    {
        return $this->repository->getParticipantDeletedThreads($user);
    }

    public function findThreadsCreatedBy(User $user): array
    {
        return $this->repository->getThreadsCreatedBy($user);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findThreadsCreatedByAdvert(Advert $advert, User $user): ?Thread
    {
        return $this->repository->findThreadsCreatedByAdvert($advert, $user);
    }

    public function markAsReadByParticipant(Thread $thread, User|UserInterface $user): void
    {
        $this->messageManager->markIsReadByThreadAndParticipant($thread, $user, true);
    }

    public function markAsUnreadByParticipant(Thread $thread, User|UserInterface $user): void
    {
        $this->messageManager->markIsReadByThreadAndParticipant($thread, $user, false);
    }

    public function saveThread(Thread $thread, $andFlush = true): void
    {
        $this->denormalize($thread);
        $this->repository->add($thread);

        if ($andFlush) {
            $this->repository->flush();
        }
    }

    public function deleteThread(Thread $thread): void
    {
        $this->repository->remove($thread, true);
    }

    /**
     * Performs denormalization tricks.
     */
    protected function denormalize(Thread $thread): void
    {
        $this->doMetadata($thread);
        $this->doCreatedByAndAt($thread);
        $this->doDatesOfLastMessageWrittenByOtherParticipant($thread);
    }

    /**
     * Ensures that the thread metadata are up to date.
     */
    protected function doMetadata(Thread $thread): void
    {
        // Participants
        foreach ($thread->getParticipants() as $participant) {
            $meta = $thread->getMetadataForParticipant($participant);
            if (!$meta) {
                $meta = $this->createThreadMetadata();
                $meta->setParticipant($participant);

                $thread->addMetadata($meta);
            }
        }

        // Messages
        foreach ($thread->getMessages() as $message) {
            $meta = $thread->getMetadataForParticipant($message->getSender());
            if (!$meta) {
                $meta = $this->createThreadMetadata();
                $meta->setParticipant($message->getSender());

                $thread->addMetadata($meta);
            }

            $meta->setLastParticipantMessageDate($message->getCreatedAt());
        }
    }

    /**
     * Ensures that the createdBy & createdAt properties are set.
     */
    protected function doCreatedByAndAt(Thread $thread): void
    {
        if (!($message = $thread->getFirstMessage())) {
            return;
        }

        if (!$thread->getCreatedAt()) {
            $thread->setCreatedAt($message->getCreatedAt());
        }

        if (!$thread->getCreatedBy()) {
            $thread->setCreatedBy($message->getSender());
        }
    }

    /**
     * Update the dates of last message written by other participants.
     */
    protected function doDatesOfLastMessageWrittenByOtherParticipant(Thread $thread): void
    {
        foreach ($thread->getMetadata() as $meta) {
            $participantId = $meta->getParticipant()->getId();
            $timestamp = 0;

            foreach ($thread->getMessages() as $message) {
                if ($participantId != $message->getSender()->getId()) {
                    $timestamp = max($timestamp, $message->getTimestamp());
                }
            }

            if ($timestamp) {
                $date = new DateTime();
                $date->setTimestamp($timestamp);
                $meta->setLastMessageDate($date);
            }
        }
    }

    #[Pure] protected function createThreadMetadata(): ThreadMetadata
    {
        return new ThreadMetadata();
    }
}