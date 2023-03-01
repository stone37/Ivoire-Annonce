<?php

namespace App\Subscriber;

use App\Repository\LoginAttemptRepository;
use App\Event\PasswordRecoveredEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PasswordResetSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoginAttemptRepository $repository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [PasswordRecoveredEvent::class => 'onPasswordRecovered'];
    }

    public function onPasswordRecovered(PasswordRecoveredEvent $event): void
    {
        $this->repository->deleteAttemptsFor($event->getUser());
    }
}
