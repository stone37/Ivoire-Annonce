<?php

namespace App\Event;

use App\Entity\Favorite;

class FavoriteCreateEvent
{
    public function  __construct(private Favorite $favorite)
    {
    }

    public function getFavorite(): Favorite
    {
        return $this->favorite;
    }
}