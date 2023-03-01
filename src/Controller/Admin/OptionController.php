<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Event\AdminCRUDEvent;
use App\Form\OptionType;
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

class OptionController extends AbstractController
{
    public function __construct(
        private ProductRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/options/{option}', name: 'app_admin_option_index')]
    public function index(Request $request, int $option): Response
    {
        $qb = $this->repository->getAdmins(Product::CATEGORY_ADVERT_OPTION, $option);

        $products = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/option/index.html.twig', [
            'products' => $products,
            'option' => $option
        ]);
    }

    #[Route(path: '/options/create/{option}', name: 'app_admin_option_create')]
    public function create(Request $request, int $option): RedirectResponse|Response
    {
        $product = (new Product())
            ->setCategory(Product::CATEGORY_ADVERT_OPTION)
            ->setOptions($option);

        $form = $this->createForm(OptionType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($product);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($product, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une option visuelle a été crée');

            return $this->redirectToRoute('app_admin_option_index', ['option' => $option]);
        }

        return $this->render('admin/option/create.html.twig', [
            'form' => $form->createView(),
            'option' => $option
        ]);
    }

    #[Route(path: '/options/{id}/edit/{option}', name: 'app_admin_option_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Product $product, int $option): Response
    {
        $form = $this->createForm(OptionType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($product);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une option visuelle a été mise à jour');

            return $this->redirectToRoute('app_admin_option_index', ['option' => $option]);
        }

        return $this->render('admin/option/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
            'option' => $option
        ]);
    }

    #[Route(path: '/options/{id}/delete', name: 'app_admin_option_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Product $product): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($product);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($product);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($product, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'L\'option visuelle a été supprimée');
            } else {
                $this->addFlash('error', 'Désolé, l\'option visuelle n\'a pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette option visuelle ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $product,
            'message' => $message,
            'configuration' => $this->configuration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/options/bulk/delete', name: 'app_admin_option_bulk_delete', options: ['expose' => true])]
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
                    $product = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($product), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($product, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les options visuelles ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les options visuelles n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' options visuelles ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette option visuelle ?';

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
            ->setAction($this->generateUrl('app_admin_option_delete', ['id' => $product->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_option_bulk_delete'))
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
