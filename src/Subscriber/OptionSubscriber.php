<?php

namespace App\Subscriber;

use App\Entity\Advert;
use App\Entity\Item;
use App\Entity\Product;
use App\Event\PaymentEvent;
use App\Repository\AdvertRepository;
use App\Storage\AdvertStorage;
use DateInterval;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AdvertStorage $storage,
        private AdvertRepository $advertRepository,
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [PaymentEvent::class => 'onPayment'];
    }

    public function onPayment(PaymentEvent $event): void
    {
        $commande = $event->getCommande();

        /** @var Item $item */
        foreach ($commande->getItems() as $item) {
            $product = $item->getProduct();

            if ($product->getCategory() === Product::CATEGORY_ADVERT_OPTION) {
                if (!$this->storage->has()) {
                    return;
                }

                $advert = $this->advertRepository->find($this->storage->get());

                if (!$advert) {
                    return;
                }

                $this->apply($advert, $product);
            }
        }
    }

    private function apply(Advert $advert, Product $product)
    {
        $now = new DateTimeImmutable();

        if ($product->getOptions() === Product::CATEGORY_ADVERT_HEAD_OPTION) {
            $end = $advert->getOptionAdvertHeadEndAt() ?: new DateTimeImmutable();
            $end = $end > $now ? $end : new DateTimeImmutable();

            $advert->setOptionAdvertHeadEndAt($end->add(new DateInterval("P{$product->getDays()}D")));
        } elseif ($product->getOptions() === Product::CATEGORY_ADVERT_URGENT_OPTION) {

            $end = $advert->getOptionAdvertUrgentEndAt() ?: new DateTimeImmutable();
            $end = $end > $now ? $end : new DateTimeImmutable();

            $advert->setOptionAdvertUrgentEndAt($end->add(new DateInterval("P{$product->getDays()}D")));

        } elseif ($product->getOptions() === Product::CATEGORY_ADVERT_HOME_GALLERY_OPTION) {

            $end = $advert->getOptionAdvertHomeGalleryEndAt() ?: new DateTimeImmutable();
            $end = $end > $now ? $end : new DateTimeImmutable();

            $advert->setOptionAdvertHomeGalleryEndAt($end->add(new DateInterval("P{$product->getDays()}D")));

        } elseif ($product->getOptions() === Product::CATEGORY_ADVERT_FEATURED_OPTION) {
            $end = $advert->getOptionAdvertFeaturedEndAt() ?: new DateTimeImmutable();
            $end = $end > $now ? $end : new DateTimeImmutable();

            $advert->setOptionAdvertFeaturedEndAt($end->add(new DateInterval("P{$product->getDays()}D")));
        }

        $this->advertRepository->flush();
    }
}



