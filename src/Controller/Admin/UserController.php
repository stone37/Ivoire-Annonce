<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Form\Filter\AdminUserType;
use App\Model\Admin\UserSearch;
use App\Repository\UserRepository;
use App\Service\UserBanService;
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
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/users', name: 'app_admin_user_index')]
    public function index(Request $request): Response
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getAdminUsers($search);

        $users = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'searchForm' => $form->createView(),
            'type' => 1
        ]);
    }

    #[Route(path: '/users/no-confirm', name: 'app_admin_user_no_confirm_index')]
    public function indexN(Request $request): Response
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getUserNoConfirmed($search);

        $users = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'searchForm' => $form->createView(),
            'type' => 2
        ]);
    }

    #[Route(path: '/users/deleted', name: 'app_admin_user_deleted_index')]
    public function indexD(Request $request): Response
    {
        $search = new UserSearch();

        $form = $this->createForm(AdminUserType::class);

        $form->handleRequest($request);

        $qb = $this->repository->getUserDeleted($search);

        $users = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'searchForm' => $form->createView(),
            'type' => 3
        ]);
    }

    #[Route(path: '/users/{id}/show/{type}', name: 'app_admin_user_show', requirements: ['id' => '\d+', 'type' => '\d+'])]
    public function show(User $user, $type): Response
    {
        return $this->render('admin/user/show.html.twig', ['user' => $user, 'type' => $type]);
    }

    #[Route(path: '/users/{id}/ban', name: 'app_admin_user_ban', requirements: ['id' => '\d+'])]
    public function ban(Request $request, UserBanService $service, User $user): RedirectResponse|JsonResponse
    {
        $service->ban($user);
        $this->repository->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json([]);
        }

        $this->addFlash('success', "L'utilisateur a été banni");

        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route(path: '/users/{id}/delete', name: 'app_admin_user_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, User $user): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($user);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($user);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($user, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le compte client a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le compte client n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        $message = 'Être vous sur de vouloir supprimer cet compte ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $user,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/users/bulk/delete', name: 'app_admin_user_bulk_delete', options: ['expose' => true])]
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
                    $user = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($user), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($user, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les comptes clients ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les clients n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            $response = new RedirectResponse($url);

            return $response;
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' comptes ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet compte ?';

        $render = $this->render('ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(User $user): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_user_delete', ['id' => $user->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_user_bulk_delete'))
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