<?php

namespace App\Subscriber;

use App\Entity\Settings;
use App\Event\UserCreatedEvent;
use App\Event\PasswordResetTokenCreatedEvent;
use App\Service\DeleteAccountService;
use App\Event\UserDeleteRequestEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AuthSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(private Mailer $mailer, SettingsManager $manager)
    {
        $this->settings = $manager->get();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetTokenCreatedEvent::class => 'onPasswordRequest',
            UserCreatedEvent::class => 'onRegister',
            UserDeleteRequestEvent::class => 'onDelete',
        ];
    }

    public function onPasswordRequest(PasswordResetTokenCreatedEvent $event): void
    {
        $email = $this->mailer->createEmail('mails/auth/password_reset.twig', [
            'token' => $event->getToken()->getToken(),
            'id' => $event->getUser()->getId(),
            'user' => $event->getUser(),
        ])
            ->to($event->getUser()->getEmail())
            ->subject($this->settings->getName().' | Réinitialisation de votre mot de passe');

        $this->mailer->send($email);
    }

    public function onRegister(UserCreatedEvent $event): void
    {
        if ($event->isUsingOauth()) {
            return;
        }

        $email = $this->mailer->createEmail('mails/auth/register.twig', ['user' => $event->getUser()])
            ->to($event->getUser()->getEmail())
            ->subject($this->settings->getName().' | Confirmation du compte');

        $this->mailer->send($email);
    }

    public function onDelete(UserDeleteRequestEvent $event): void
    {
        $email = $this->mailer->createEmail('mails/auth/delete.twig', [
            'user' => $event->getUser(),
            'days' => DeleteAccountService::DAYS])
            ->to($event->getUser()->getEmail())
            ->subject($this->settings->getName().' | Suppression de votre compte');

        $this->mailer->send($email);
    }
}
