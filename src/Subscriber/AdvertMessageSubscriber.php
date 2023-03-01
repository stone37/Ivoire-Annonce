<?php

namespace App\Subscriber;

use App\Event\ThreadMessageEvent;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdvertMessageSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private NotificationService $service,
        private UrlGeneratorInterface $url
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [ThreadMessageEvent::class => 'onCreated'];
    }

    public function onCreated(ThreadMessageEvent $event): void
    {
        $message = $event->getMessage();

        $userName = htmlentities($message->getSender()->getFirstname());

        $messageText = htmlentities($message->getBody());
        $wording = '%s vous a envoyé un message %s';

        $user = null;

        foreach($message->getThread()->getParticipants() as $participant) {
            if ($message->getSender()->getId() != $participant->getId()) {
                $user = $participant;
            }
        }

        if ($user == null) {
            return;
        }

        $this->service->notifyUser(
            $user,
            sprintf($wording, "<strong>$userName</strong>", "« $messageText »"),
            $message->getThread()->getAdvert(),
            $this->url->generate('app_user_thread_show', ['id' => $message->getThread()->getId()])
        );
    }
}
