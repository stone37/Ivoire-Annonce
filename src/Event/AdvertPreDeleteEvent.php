<?php

namespace App\Event;

use App\Entity\Advert;
use Symfony\Component\HttpFoundation\Response;

class AdvertPreDeleteEvent
{
    private ?Response $response = null;

    public function  __construct(private Advert $advert)
    {
    }

    public function getAdvert(): Advert
    {
        return $this->advert;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response)
    {
        $this->response = $response;
    }
}