<?php

namespace App\Builder;

use App\Entity\Advert;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

class NewThreadMessageBuilder extends AbstractMessageBuilder
{
    public function setAdvert(Advert $advert): self
    {
        $this->thread->setAdvert($advert);

        return $this;
    }

    public function addRecipient(User|UserInterface $recipient): self
    {
        $this->thread->addParticipant($recipient);

        return $this;
    }

    public function addRecipients(Collection $recipients): self
    {
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }

        return $this;
    }
}