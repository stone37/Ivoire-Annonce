<?php

namespace App\Controller\Admin;

use App\Mailing\Mailer;
use App\Controller\Traits\ControllerTrait;
use App\Repository\AdvertRepository;
use App\Repository\NewsletterDataRepository;
use App\Repository\PaymentRepository;
use App\Repository\ReviewRepository;
use App\Repository\SuggestionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private AdvertRepository $advertRepository,
        private UserRepository $userRepository,
        private PaymentRepository $paymentRepository,
        private ReviewRepository $reviewRepository,
        private SuggestionRepository $suggestionRepository,
        private NewsletterDataRepository $newsletterDataRepository
    )
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route(path: '/admin', name: 'app_admin_index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'advertValidateNumber' => $this->advertRepository->validatedNumber(),
            'advertNewNumber' => $this->advertRepository->newNumber(),
            'advertDeniedNumber' => $this->advertRepository->deniedNumber(),
            'advertRemoveNumber' => $this->advertRepository->removeNumber(),
            'advertArchiveNumber' => $this->advertRepository->archiveNumber(),
            'users' => $this->userRepository->getUserNumber(),
            'lastClients' => $this->userRepository->getLastClients(),
            'lastOrders' => $this->paymentRepository->getLasts(),
            'months' => $this->paymentRepository->getMonthlyRevenues(),
            'days' => $this->paymentRepository->getDailyRevenues(),
            'orders' => $this->paymentRepository->getNumber(),
            'reviews' => $this->reviewRepository->getNumber(),
            'suggestions' => $this->suggestionRepository->getNumber(),
            'newsletterData' => $this->newsletterDataRepository->getNumber()
        ]);
    }

    /**
     * Envoie un email de test à mail-tester pour vérifier la configuration du serveur.
     */
    #[Route(path: '/admin/mailtester', name: 'app_admin_mailtest', methods: ['POST'])]
    public function testMail(Request $request, Mailer $mailer): RedirectResponse
    {
        $email = $mailer->createEmail('mails/auth/register.twig', [
            'user' => $this->getUserOrThrow(),
        ])
            ->to($request->get('email'))
            ->subject('Hotel particulier | Confirmation du compte');
        $mailer->send($email);

        $this->addFlash('success', "L'email de test a bien été envoyé");

        return $this->redirectToRoute('app_admin_index');
    }

}
