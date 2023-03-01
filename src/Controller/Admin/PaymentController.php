<?php

namespace App\Controller\Admin;

use App\Entity\Payment;
use App\Event\AdminCRUDEvent;
use App\Event\PaymentRefundedEvent;
use App\Repository\PaymentRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class PaymentController extends AbstractController
{
    public function __construct(
        private PaymentRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/payments', name: 'app_admin_payment_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->findBy([], ['createdAt' => 'desc']);

        $payments = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/payment/index.html.twig', [
            'payments' => $payments
        ]);
    }

    #[Route(path: '/payments/{id}/refunded', name: 'app_admin_payment_refunded', requirements: ['id' => '\d+'])]
    public function refunded(Payment $payment): RedirectResponse
    {
        $this->dispatcher->dispatch(new PaymentRefundedEvent($payment));
        $this->addFlash('success', 'Le paiement a bien été marqué comme remboursé');

        return $this->redirectToRoute('app_admin_payment_index');
    }

    #[Route(path: '/payments/report', name: 'app_admin_payment_report')]
    public function report(Request $request): Response
    {
        $year = $request->query->getInt('year', (int) date('Y'));

        return $this->render('admin/payment/report.html.twig', [
            'reports' => $this->repository->getMonthlyReport($year),
            'prefix' => 'admin_transaction',
            'current_year' => date('Y'),
            'year' => $year,
        ]);
    }

    #[Route(path: '/payments/{id}/delete', name: 'app_admin_payment_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Payment $payment): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($payment);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($payment);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($payment, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le paiement a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le paiement n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cet paiement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $payment,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/payments/bulk/delete', name: 'app_admin_payment_bulk_delete', options: ['expose' => true])]
    public function deleteBulk(Request $request): RedirectResponse|JsonResponse
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data'))
            $request->getSession()->set('data', $ids);

        $form = $this->deleteMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $payment = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($payment), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($payment, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les paiements ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les paiements n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' paiements ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet paiement ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Payment $payment): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payment_delete', ['id' => $payment->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_payment_bulk_delete'))
            ->getForm();
    }

    #[ArrayShape(['modal' => "\string[][]"])] private function configuration(): array
    {
        return [
            'modal' => [
                'delete' => [
                    'type' => 'modal-danger',
                    'icon' => 'fas fa-times',
                    'yes_class' => 'btn-outline-danger',
                    'no_class' => 'btn-danger'
                ]
            ]
        ];
    }
}




