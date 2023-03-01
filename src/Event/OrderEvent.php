<?php


namespace App\Event;

use App\Entity\Commande;

class OrderEvent
{
    public function __construct(private Commande $commande)
    {
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }
}

