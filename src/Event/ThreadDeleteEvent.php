<?php

namespace App\Event;

use App\Entity\Thread;

class ThreadDeleteEvent
{
    public function __construct(private Thread $thread)
    {
    }

    public function getThread(): Thread
    {
        return $this->thread;
    }
}
