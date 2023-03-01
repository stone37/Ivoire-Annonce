<?php

namespace App\Service;

use App\Builder\NewThreadMessageBuilder;
use App\Builder\ReplyMessageBuilder;
use App\Entity\Thread;
use App\Manager\MessageManager;
use App\Manager\ThreadManager;

class Composer
{
    public function __construct(
        private MessageManager $messageManager,
        private ThreadManager $threadManager
    )
    {
    }

    public function newThread(): NewThreadMessageBuilder
    {
        $thread = $this->threadManager->createThread();
        $message = $this->messageManager->createMessage();

        return new NewThreadMessageBuilder($message, $thread);
    }

    public function reply(Thread $thread): ReplyMessageBuilder
    {
        $message = $this->messageManager->createMessage();

        return new ReplyMessageBuilder($message, $thread);
    }
}