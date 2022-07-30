<?php

namespace App\Entity\Traits;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait Notifiable
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $notificationsReadAt = null;

    public function getNotificationsReadAt(): ?DateTimeInterface
    {
        return $this->notificationsReadAt;
    }

    public function setNotificationsReadAt(?DateTimeInterface $notificationsReadAt): void
    {
        $this->notificationsReadAt = $notificationsReadAt;
    }
}
