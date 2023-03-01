<?php

namespace App\Subscriber;

use App\Entity\AdvertRead;
use App\Event\AdvertBadEvent;
use App\Event\AdvertCreateEvent;
use App\Event\AdvertInitEvent;
use App\Event\AdvertViewEvent;
use App\Exception\CategoryNotFoundException;
use App\Manager\AdvertManager;
use App\Manager\OrphanageManager;
use App\Repository\AdvertReadRepository;
use App\Repository\CategoryRepository;
use App\Storage\AdvertStorage;
use App\Storage\CartStorage;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

class AdvertSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CategoryRepository $repository,
        private AdvertReadRepository $advertReadRepository,
        private AdvertManager $manager,
        private CartStorage $cartStorage,
        private AdvertStorage $advertStorage,
        private OrphanageManager $orphanageManager,
        private UrlGeneratorInterface $urlGenerator,
        private Security $security
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            AdvertInitEvent::class => 'onInit',
            AdvertCreateEvent::class => 'onCreated',
            AdvertBadEvent::class => 'onError',
            AdvertViewEvent::class => 'onView',
        ];
    }

    /**
     * @throws NonUniqueResultException
     */
    public function onInit(AdvertInitEvent $event)
    {
        $request = $event->getRequest();

        if ($request->isMethod('GET')) {
            $category = $this->repository->getEnabledBySlug($request->attributes->get('category_slug'));

            if (null === $category) {
                throw new CategoryNotFoundException();
            }

            $request->getSession()->set($this->provideKey(), []);
            $this->orphanageManager->initClear($request->getSession());
            $this->cartStorage->init();
            $this->advertStorage->remove();
        }
    }

    public function onCreated(AdvertCreateEvent $event)
    {
        if ($this->cartStorage->has() && !empty($this->cartStorage->get())) {
            $this->advertStorage->set($event->getAdvert()->getId());

            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_cart_validate')));
        }
    }

    public function onError(AdvertBadEvent $event)
    {
        $request = $event->getRequest();

        if ($event->getRequest()->isMethod('POST')) {
            $this->orphanageManager->initClear($request->getSession());
            $request->getSession()->set($this->provideKey(), []);
            $this->cartStorage->init();
        }
    }

    public function onView(AdvertViewEvent $event): void
    {
        $advert = $event->getAdvert();

        if ($this->security->getUser() === $advert->getOwner()) {
            return;
        }

        $read = (new AdvertRead())
            ->setAdvert($advert);

        $this->advertReadRepository->add($read, true);
    }




    private function provideKey(): string
    {
        return '_app_advert_images';
    }
}
