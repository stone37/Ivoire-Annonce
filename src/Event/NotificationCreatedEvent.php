<?php

namespace App\Event;

use App\Entity\Notification;

class NotificationCreatedEvent
{
    public function __construct(private Notification $notification)
    {
    }

    public function getNotification(): Notification
    {
        return $this->notification;
    }
}