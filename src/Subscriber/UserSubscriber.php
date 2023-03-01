<?php

namespace App\Subscriber;

use App\Event\UserBannedEvent;
use App\Repository\AdvertRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(private AdvertRepository $advertRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [UserBannedEvent::class => 'cleanUserContent'];
    }

    public function cleanUserContent(UserBannedEvent $event): void
    {
        $this->advertRepository->deleteForUser($event->getUser());
    }
}
