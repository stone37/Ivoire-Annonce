<?php

namespace App\Builder;

use App\Entity\Thread;
use App\Entity\ThreadMessage;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractMessageBuilder
{
    public function __construct(protected ThreadMessage $message, protected Thread $thread)
    {
        $thread->addMessage($message);
    }

    public function getMessage(): ThreadMessage
    {
        return $this->message;
    }

    public function setBody(string $body): self
    {
        $this->message->setBody($body);

        return $this;
    }

    public function setSender(User|UserInterface $sender): self
    {
        $this->message->setSender($sender);
        $this->thread->addParticipant($sender);

        return $this;
    }
}
