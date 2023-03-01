<?php

namespace App\Twig;

use App\Util\MessageUtil;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MessageExtension extends AbstractExtension
{
    public function __construct(private MessageUtil $util)
    {
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('app_message_is_read', array($this->util, 'isRead')),
            new TwigFunction('app_message_nb_unread', array($this->util, 'getNbUnread')),
            new TwigFunction('app_message_can_delete_thread', array($this->util, 'canDeleteThread')),
            new TwigFunction('app_message_deleted_by_participant', array($this->util, 'isThreadDeletedByParticipant'))
        );
    }
}