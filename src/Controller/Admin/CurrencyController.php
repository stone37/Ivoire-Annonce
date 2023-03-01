<?php

namespace App\Controller\Admin;

use App\Entity\Currency;
use App\Event\AdminCRUDEvent;
use App\Form\CurrencyType;
use App\Repository\CurrencyRepository;
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
class CurrencyController extends AbstractController
{
    public function __construct(
        private CurrencyRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/currencies', name: 'app_admin_currency_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->findBy([], ['createdAt' => 'desc']);

        $currencies = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/currency/index.html.twig', [
            'currencies' => $currencies,
        ]);
    }

    #[Route(path: '/currencies/create', name: 'app_admin_currency_create')]
    public function create(Request $request): RedirectResponse|Response
    {
        $currency = new Currency();

        $form = $this->createForm(CurrencyType::class, $currency);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($currency);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($currency, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une devise a été crée');

            return $this->redirectToRoute('app_admin_currency_index');
        }

        return $this->render('admin/currency/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/currencies/{id}/edit', name: 'app_admin_currency_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Currency $currency): RedirectResponse|Response
    {
        $form = $this->createForm(CurrencyType::class, $currency);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($currency);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une devise a été mise à jour');

            return $this->redirectToRoute('app_admin_currency_index');
        }

        return $this->render('admin/currency/edit.html.twig', [
            'form' => $form->createView(),
            'currency' => $currency,
        ]);
    }

    #[Route(path: '/currencies/{id}/delete', name: 'app_admin_currency_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Currency $currency): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($currency);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($currency);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($currency, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'La devise a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la devise n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette devise ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $currency,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/currencies/bulk/delete', name: 'app_admin_currency_bulk_delete', options: ['expose' => true])]
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
                    $currency = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($currency), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($currency, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les devises ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les devises n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' devises ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette devise ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Currency $currency): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_currency_delete', ['id' => $currency->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_currency_bulk_delete'))
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

