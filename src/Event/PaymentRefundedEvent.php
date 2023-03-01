<?php

namespace App\Event;


use App\Entity\Payment;

class PaymentRefundedEvent
{
    public function __construct(private Payment $payment)
    {
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }
}

