<?php

namespace App\Controller\Admin;

use App\Entity\CategoryPremium;
use App\Event\AdminCRUDEvent;
use App\Form\CategoryPremiumType;
use App\Form\Filter\AdminCategoryType;
use App\Model\Admin\CategorySearch;
use App\Repository\CategoryPremiumRepository;
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
class CategoryPremiumController extends AbstractController
{
    public function __construct(
        private CategoryPremiumRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/categories/premium', name: 'app_admin_category_premium_index')]
    public function index(Request $request): Response
    {
        $search = new CategorySearch();
        $form = $this->createForm(AdminCategoryType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getAdmins($search);

        $categories = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/categoryPremium/index.html.twig', [
            'categories' => $categories,
            'searchForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/categories/premium/create', name: 'app_admin_category_premium_create')]
    public function create(Request $request): RedirectResponse|Response
    {
        $category = new CategoryPremium();
        $form = $this->createForm(CategoryPremiumType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($category);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($category, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une catégorie premium a été crée');

            return $this->redirectToRoute('app_admin_category_premium_index');
        }

        return $this->render('admin/categoryPremium/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/categories/premium/{id}/edit', name: 'app_admin_category_premium_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, CategoryPremium $category): RedirectResponse|Response
    {
        $form = $this->createForm(CategoryPremiumType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($category);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une catégorie premium a été mise à jour');

            return $this->redirectToRoute('app_admin_category_premium_index');
        }

        return $this->render('admin/categoryPremium/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[Route(path: '/categories/premium/{id}/move', name: 'app_admin_category_premium_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, CategoryPremium $category): RedirectResponse
    {
        if ($request->query->has('pos')) {
            $pos = ($category->getPosition() + (int) $request->query->get('pos'));

            if ($pos >= 0) {
                $category->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_category_premium_index');
    }

    #[Route(path: '/categories/premium/{id}/delete', name: 'app_admin_category_premium_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, CategoryPremium $category): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($category);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($category);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($category, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'La catégorie premium a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la catégorie premium n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette catégorie premium ?';

        $render = $this->render('ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $category,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/categories/premium/bulk/delete', name: 'app_admin_category_premium_bulk_delete', options: ['expose' => true])]
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
                    $category = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($category), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($category, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les catégories premium ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les catégories premium n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' catégories premium ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette catégorie premium ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(CategoryPremium $category): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_category_premium_delete', ['id' => $category->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_category_premium_bulk_delete'))
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
