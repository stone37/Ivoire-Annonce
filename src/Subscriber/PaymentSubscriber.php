<?php

namespace App\Subscriber;

use App\Entity\Payment;
use App\Entity\Settings;
use App\Event\PaymentEvent;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use App\Service\NotificationService;
use App\Service\Summary;
use App\Service\UniqueNumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentSubscriber implements EventSubscriberInterface
{
    private ?Settings $settings;

    public function __construct(
        private Mailer $mailer,
        private NotificationService $service,
        private UrlGeneratorInterface $generator,
        private UniqueNumberGenerator $numberGenerator,
        private EntityManagerInterface $em,
        SettingsManager $manager
    )
    {
        $this->settings = $manager->get();
    }

    public static function getSubscribedEvents(): array
    {
        return [PaymentEvent::class => 'onPayment'];
    }

    public function onPayment(PaymentEvent $event)
    {
        $commande = $event->getCommande();
        $commande->setValidated(true)
            ->setReference($this->numberGenerator->generate(10, false));

        $summary = new Summary($commande);
        $user = $commande->getOwner();

        // On enregistre la transaction
        $payment = (new Payment())
            ->setCommande($commande)
            ->setPrice($summary->amountPaid())
            ->setTaxe($summary->getTaxeAmount())
            ->setDiscount($summary->getDiscount())
            ->setFirstname($user->getFirstname())
            ->setLastname($user->getLastname())
            ->setEmail($user->getEmail())
            ->setPhone($user->getPhone())
            ->setCountry($user->getCountry())
            ->setCity($user->getCity())
            ->setEnabled(true);

        $this->em->persist($payment);
        $this->em->flush();

        $email = $this->mailer->createEmail('mails/commande/validate.twig', ['commande' => $commande])
                    ->to($user->getEmail())
                    ->subject($this->settings->getName() . ' | Commande validée #' . $commande->getReference());

        $this->mailer->send($email);

        $wording = 'Votre achat a bien été valider';
        $url = $this->generator->generate('app_user_invoice_index');
        $this->service->notifyUser($user, $wording, $event->getCommande(), $url);
    }
}

