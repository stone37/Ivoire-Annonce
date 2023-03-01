<?php

namespace App\Event;

use App\Entity\Advert;
use Symfony\Component\HttpFoundation\Request;

class AdvertBadEvent
{
    public function  __construct(private Advert $advert, private Request $request)
    {
    }

    public function getAdvert(): Advert
    {
        return $this->advert;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
