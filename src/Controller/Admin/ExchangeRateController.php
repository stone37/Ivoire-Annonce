<?php

namespace App\Controller\Admin;

use App\Entity\ExchangeRate;
use App\Event\AdminCRUDEvent;
use App\Form\ExchangeRateType;
use App\Repository\ExchangeRateRepository;
use JetBrains\PhpStorm\ArrayShape;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class ExchangeRateController extends AbstractController
{
    public function __construct(
        private ExchangeRateRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/exchange-rates', name: 'app_admin_exchange_rate_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->findBy([], ['createdAt' => 'desc']);

        $exchanges = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/exchange/index.html.twig', [
            'exchanges' => $exchanges
        ]);
    }

    #[Route(path: '/exchange-rates/create', name: 'app_admin_exchange_rate_create')]
    public function create(Request $request): RedirectResponse|Response
    {
        $exchange = new ExchangeRate();

        $form = $this->createForm(ExchangeRateType::class, $exchange);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($exchange);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($exchange, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un taux de change a été crée');

            return $this->redirectToRoute('app_admin_exchange_rate_index');
        }

        return $this->render('admin/exchange/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/exchange-rates/{id}/edit', name: 'app_admin_exchange_rate_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, ExchangeRate $exchange): RedirectResponse|Response
    {
        $form = $this->createForm(ExchangeRateType::class, $exchange);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($exchange);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un taux de change a été mise à jour');

            return $this->redirectToRoute('app_admin_exchange_rate_index');
        }

        return $this->render('admin/exchange/edit.html.twig', [
            'form' => $form->createView(),
            'exchange' => $exchange,
        ]);
    }

    #[Route(path: '/exchange-rates/{id}/delete', name: 'app_admin_exchange_rate_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, ExchangeRate $exchange): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($exchange);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($exchange);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($exchange, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le taux de change a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le taux de change n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cet taux de change ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $exchange,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/exchange-rates/bulk/delete', name: 'app_admin_exchange_rate_bulk_delete', options: ['expose' => true])]
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
                    $exchange = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($exchange), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($exchange, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les taux de changes ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les taux de changes n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' taux de changes ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet taux de change ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(ExchangeRate $exchange): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_exchange_rate_delete', ['id' => $exchange->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_exchange_rate_bulk_delete'))
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

