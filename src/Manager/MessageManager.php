<?php

namespace App\Manager;

use App\Entity\Thread;
use App\Entity\ThreadMessage;
use App\Entity\ThreadMessageMetadata;
use App\Entity\User;
use App\Repository\ThreadMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use JetBrains\PhpStorm\Pure;
use PDO;

class MessageManager
{
    public function __construct(
        private ThreadMessageRepository $repository,
        private EntityManagerInterface $em
    )
    {
    }

    public function createMessage(): ThreadMessage
    {
        return new ThreadMessage();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getNbUnreadMessageByParticipant(User $user): int
    {
        return $this->repository->getNbUnreadMessageByParticipant($user);
    }

    public function markIsReadByThreadAndParticipant(Thread $thread, User $user, $isRead): void
    {
        foreach ($thread->getMessages() as $message) {
            $this->markIsReadByParticipant($message, $user, $isRead);
        }
    }

    public function saveMessage(ThreadMessage $message, $andFlush = true): void
    {
        $this->denormalize($message);
        $this->repository->add($message);

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * Marks the message as read or unread by this participant.
     */
    protected function markIsReadByParticipant(ThreadMessage $message, User $user, $isRead): void
    {
        $meta = $message->getMetadataForParticipant($user);

        if (!$meta || $meta->isIsRead() == $isRead) {
            return;
        }

        $this->em->createQueryBuilder()
            ->update(ThreadMessageMetadata::class, 'm')
            ->set('m.isRead', '?1')
            ->setParameter('1', (bool) $isRead, PDO::PARAM_BOOL)
            ->where('m.id = :id')
            ->setParameter('id', $meta->getId())
            ->getQuery()
            ->execute();
    }

    /**
     * Performs denormalization tricks.
     */
    protected function denormalize(ThreadMessage $message): void
    {
        $this->doMetadata($message);
    }

    /**
     * Ensures that the message metadata are up to date.
     */
    protected function doMetadata(ThreadMessage $message): void
    {
        foreach ($message->getThread()->getMetadata() as $threadMeta) {
            $meta = $message->getMetadataForParticipant($threadMeta->getParticipant());
            if (!$meta) {
                $meta = $this->createMessageMetadata();
                $meta->setParticipant($threadMeta->getParticipant());

                $message->addMetadata($meta);
            }
        }
    }

    #[Pure] protected function createMessageMetadata(): ThreadMessageMetadata
    {
        return new ThreadMessageMetadata();
    }
}
