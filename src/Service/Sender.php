<?php

namespace App\Service;

use App\Entity\ThreadMessage;
use App\Event\ThreadMessageEvent;
use App\Manager\MessageManager;
use App\Manager\ThreadManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Sender
{
    public function __construct(
        private ThreadManager $threadManager,
        private MessageManager $messageManager,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    public function send(ThreadMessage $message)
    {
        $this->threadManager->saveThread($message->getThread(), false);
        $this->messageManager->saveMessage($message, false);

        $message->getThread()->setIsDeleted(false);

        $this->messageManager->saveMessage($message);

        $this->dispatcher->dispatch(new ThreadMessageEvent($message));
    }

    public function sendApi(ThreadMessage $message)
    {
        $this->threadManager->saveThread($message->getThread(), false);
        $this->messageManager->saveMessage($message, false);

        $message->getThread()->setIsDeleted(false);
        $this->messageManager->saveMessage($message, false);

        $this->dispatcher->dispatch(new ThreadMessageEvent($message));
    }

}