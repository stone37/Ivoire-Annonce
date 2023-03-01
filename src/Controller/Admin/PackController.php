<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Event\AdminCRUDEvent;
use App\Form\PackType;
use App\Repository\ProductRepository;
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

class PackController extends AbstractController
{
    public function __construct(
        private ProductRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/packs', name: 'app_admin_pack_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->getAdmins(Product::CATEGORY_PREMIUM_PACK);

        $packs = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/pack/index.html.twig', [
            'packs' => $packs
        ]);
    }

    #[Route(path: '/packs/create', name: 'app_admin_pack_create')]
    public function create(Request $request): RedirectResponse|Response
    {
        $pack = (new Product())->setCategory(Product::CATEGORY_PREMIUM_PACK);

        $form = $this->createForm(PackType::class, $pack);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($pack);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($pack, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un pack a été crée');

            return $this->redirectToRoute('app_admin_pack_index');
        }

        return $this->render('admin/pack/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/packs/{id}/edit', name: 'app_admin_pack_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Product $pack): Response
    {
        $form = $this->createForm(PackType::class, $pack);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($pack);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un pack a été mise à jour');

            return $this->redirectToRoute('app_admin_pack_index');
        }

        return $this->render('admin/pack/edit.html.twig', [
            'form' => $form->createView(),
            'pack' => $pack
        ]);
    }

    #[Route(path: '/packs/{id}/delete', name: 'app_admin_pack_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Product $pack): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($pack);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($pack);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($pack, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le pack a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le pack n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cet pack ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $pack,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/packs/bulk/delete', name: 'app_admin_pack_bulk_delete', options: ['expose' => true])]
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
                    $pack = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($pack), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($pack, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les packs ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les packs n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' packs ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet pack ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Product $product): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_pack_delete', ['id' => $product->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_pack_bulk_delete'))
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

