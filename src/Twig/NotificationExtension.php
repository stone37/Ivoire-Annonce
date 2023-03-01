<?php

namespace App\Twig;

use App\Service\NotificationService;
use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NotificationExtension extends AbstractExtension
{
    public function __construct(private Security $security,  private NotificationService $service)
    {
    }

    public function getFunctions()
    {
        return array(
            new TwigFunction('app_notification_nb_unread', array($this, 'getNbUnread')),
        );
    }

    public function getNbUnread(): int
    {
        return $this->service->nbUnread($this->security->getUser());
    }
}