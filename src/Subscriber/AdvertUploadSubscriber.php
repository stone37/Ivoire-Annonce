<?php

namespace App\Subscriber;

use App\Entity\AdvertPicture;
use App\Event\AdvertPreCreateEvent;
use App\Event\AdvertPreEditEvent;
use App\Repository\AdvertPictureRepository;
use App\Service\UploadService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;

class AdvertUploadSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UploadService $upload,
        private AdvertPictureRepository $advertPictureRepository
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            AdvertPreCreateEvent::class => 'onUpload',
            AdvertPreEditEvent::class => 'onEditUpload',
        ];
    }

    public function onEditUpload(AdvertPreEditEvent $event)
    {
        $advert  = $event->getAdvert();
        $request = $event->getRequest();

        $images = $this->upload->getFilesUpload($request->getSession());
        $session = $request->getSession()->get($this->provideKey());

        $principale = [];
        $has_principale = false;

        foreach ($session as $values) {
            foreach ($values as $key => $value) {
                $principale[$key] = $value;
                if ($value == 1) {
                    $has_principale = true;
                }
            }
        }

        if ($has_principale) {
            /** @var AdvertPicture $picture */
            foreach ($advert->getImages() as $picture) {
                $picture->setPrincipale(false);
            }
        }

        foreach ($images as $image) {
            $picture = (new AdvertPicture())
                ->setFile(new File($image->getPathname()))
                ->setPrincipale((bool)$principale[$image->getFilename()]);

            $this->advertPictureRepository->add($picture, false);

            $advert->addImage($picture);
        }
    }

    public function onUpload(AdvertPreCreateEvent $event)
    {
        $advert  = $event->getAdvert();
        $request = $event->getRequest();

        $images = $this->upload->getFilesUpload($request->getSession());
        $session = $request->getSession()->get($this->provideKey());
        $principale = [];

        foreach ($session as $values) {
            foreach ($values as $key => $value) {
                $principale[$key] = $value;
            }
        }

        foreach ($images as $image) {
            $picture = (new AdvertPicture())
                ->setFile(new File($image->getPathname()))
                ->setPrincipale((bool)$principale[$image->getFilename()]);

            $advert->addImage($picture);
        }
    }

    private function provideKey(): string
    {
        return '_app_advert_images';
    }
}
