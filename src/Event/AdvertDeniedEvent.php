<?php

namespace App\Event;

use App\Entity\Advert;

class AdvertDeniedEvent
{
    public function __construct(private Advert $advert)
    {
    }

    public function getAdvert(): Advert
    {
        return $this->advert;
    }
}