<?php

namespace App\Subscriber;

use App\Entity\Settings;
use App\Event\AdvertCreateEvent;
use App\Event\AdvertDeniedEvent;
use App\Event\AdvertValidatedEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdvertStateSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(
        private Mailer $mailer,
        private NotificationService $service,
        private UrlGeneratorInterface $urlGenerator,
        SettingsManager $settingsManager
    )
    {
        $this->settings = $settingsManager->get();
    }

    public static function getSubscribedEvents()
    {
        return [
            AdvertCreateEvent::class => 'onCreated',
            AdvertValidatedEvent::class => 'onValidated',
            AdvertDeniedEvent::class => 'onDenied'
        ];
    }

    public function onCreated(AdvertCreateEvent $event)
    {
        $advert = $event->getAdvert();

        $email = $this->mailer->createEmail('mails/advert/new.twig', ['advert' => $advert])
                    ->to($advert->getOwner()->getEmail())
                    ->subject($this->settings->getName() . ' | Création d\'une nouvelle annonce');

        $this->mailer->send($email);

        $wording = 'Création d\'une nouvelle annonce';
        $url = $this->urlGenerator->generate('app_admin_advert_show', ['id' => $advert->getId(), 'type' => 1]);

        $this->service->notifyChannel('admin', $wording, $advert, $url);
    }

    public function onValidated(AdvertValidatedEvent $event)
    {
        $advert = $event->getAdvert();

        $email = $this->mailer->createEmail('mails/advert/validate.twig', ['advert' => $advert])
                    ->to($advert->getOwner()->getEmail())
                    ->subject($this->settings->getName() . ' | Validation d\'une annonce');

        $this->mailer->send($email);

        $wording = 'Votre annonce a été valider';
        $url = $this->urlGenerator->generate('app_advert_show', [
            'category_slug' => $advert->getCategory()->getSlug(),
            'sub_category_slug' => $advert->getSubCategory()->getSlug(),
            'city' => $advert->getLocation()->getCity(),
            'slug' => $advert->getSlug(),
            'reference' => $advert->getReference()
        ]);

        $this->service->notifyUser($advert->getOwner(), $wording, $advert, $url);
    }

    public function onDenied(AdvertDeniedEvent $event)
    {
        $advert = $event->getAdvert();

        $email = $this->mailer->createEmail('mails/advert/denied.twig', ['advert' => $advert])
                    ->to($advert->getOwner()->getEmail())
                    ->subject($this->settings->getName().' | Rejet de votre annonce');

        $this->mailer->send($email);

        $title = htmlentities($advert->getTitle());
        $wording = 'Votre annonce %s a été rejeter';

        $this->service->notifyUser($advert->getOwner(), sprintf($wording, "« $title »"), $advert);
    }
}
