<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Event\NotificationCreatedEvent;
use App\Event\NotificationReadEvent;
use App\Repository\NotificationRepository;
use DateTime;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationService
{
    public function __construct(
        private NotificationRepository $repository,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function notifyChannel(string $channel, string $message, ?object $entity = null, string $url = null): Notification
    {
        $notification = (new Notification())
            ->setMessage($message)
            ->setUrl($url)
            ->setTarget($entity ? $this->getHashForEntity($entity) : null)
            ->setCreatedAt(new DateTime())
            ->setChannel($channel);

        $this->repository->add($notification, true);

        $this->dispatcher->dispatch(new NotificationCreatedEvent($notification));

        return $notification;
    }

    public function notifyUser(User $user, string $message, object $entity, string $url = null): Notification
    {
        $notification = (new Notification())
            ->setMessage($message)
            ->setTarget($this->getHashForEntity($entity))
            ->setUrl($url)
            ->setCreatedAt(new DateTime())
            ->setOwner($user);

        //$this->repository->persistOrUpdate($notification);
        $this->repository->add($notification, true);
        $this->dispatcher->dispatch(new NotificationCreatedEvent($notification));

        return $notification;
    }

    public function forUser(User|UserInterface $user): array
    {
        return $this->repository->findRecentForUser($user);
    }

    public function forChannel(array $channel): array
    {
        return $this->repository->findRecentForChannel($channel);
    }

    public function readAll(User|UserInterface $user): void
    {
        $user->setNotificationsReadAt(new DateTime());
        $this->repository->flush();
        $this->dispatcher->dispatch(new NotificationReadEvent($user));
    }

    public function nbUnread(User|UserInterface $user): int
    {
        $count = 0;
        $notifications = $this->repository->findRecentForUser($user);

        foreach($notifications as $notification) {
            if ($notification->isRead()) $count++;
        }

        return $count;
    }

    public function unreadForUser(User $user): array
    {
        $results = [];

        $notifications = $this->repository->findRecentForUser($user);

        foreach($notifications as $notification) {
            if ($notification->isRead()) $results[] = $notification;
        }

        return $results;
    }

    private function getHashForEntity(object $entity): string
    {
        $hash = get_class($entity);
        if (method_exists($entity, 'getId')) {
            $hash .= '::'. $entity->getId();
        }

        return $hash;
    }
}