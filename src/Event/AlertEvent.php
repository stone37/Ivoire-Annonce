<?php

namespace App\Event;

use App\Entity\Alert;

class AlertEvent
{
    public function __construct(private Alert $alert)
    {
    }

    public function getAlert(): Alert
    {
        return $this->alert;
    }
}