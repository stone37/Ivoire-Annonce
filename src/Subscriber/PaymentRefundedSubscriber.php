<?php

namespace App\Subscriber;

use App\Event\PaymentRefundedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentRefundedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [PaymentRefundedEvent::class => 'onPaymentReimbursed'];
    }

    public function onPaymentReimbursed(PaymentRefundedEvent $event): void
    {
        $payment = $event->getPayment();

        if ($payment->isRefunded()) {
            return;
        }

        $payment->setRefunded(true);
        $this->em->flush();
    }
}
