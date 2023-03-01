<?php

namespace App\Subscriber;

use App\Entity\Commande;
use App\Entity\Product;
use App\Event\PaymentEvent;
use App\Service\TransactionService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreditSubscriber implements EventSubscriberInterface
{
    public function __construct(private TransactionService $transaction)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [PaymentEvent::class => 'onPayment'];
    }

    public function onPayment(PaymentEvent $event)
    {
        $commande = $event->getCommande();

        if ($commande->getPaymentMethod() === Commande::CARD_PAYMENT) {
            /** @var Product $product */
            $product = $commande->getItems()->first()->getProduct();

            if ($product->getCategory() === Product::CATEGORY_CREDIT) {
                $amount = $product->getPrice() + $product->getAmount();

                $this->transaction->addMoney($commande->getOwner(), $amount, 'Paiement de credit');
            }
        }
    }
}



