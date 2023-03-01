<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Repository\PaymentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class InvoiceController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private PaymentRepository $paymentRepository,
        private PaginatorInterface $paginator
    )
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/invoices', name: 'app_user_invoice_index')]
    public function index(Request $request): Response
    {
        $user = $this->getUserOrThrow();

        $payments = $this->paymentRepository->findFor($user);
        $payments = $this->paginator->paginate($payments, $request->query->getInt('page', 1), 20);

        return $this->render('user/invoice/index.html.twig', [
            'user' => $user,
            'payments' => $payments
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/invoices/{id}', name: 'app_user_invoice_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $payment = $this->paymentRepository->findForId($id, $this->getUser());

        if (null === $payment) {
            throw new NotFoundHttpException();
        }

        return $this->render('user/invoice/show.html.twig', [
            'payment' => $payment,
        ]);
    }
}


