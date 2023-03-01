<?php

namespace App\Event;

use App\Entity\ThreadMessage;

class ThreadMessageEvent
{
    public function __construct(private ThreadMessage $message)
    {
    }

    public function getMessage(): ThreadMessage
    {
        return $this->message;
    }
}