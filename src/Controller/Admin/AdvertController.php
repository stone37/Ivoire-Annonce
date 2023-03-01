<?php

namespace App\Controller\Admin;

use App\Entity\Advert;
use App\Entity\User;
use App\Event\AdminCRUDEvent;
use App\Event\AdvertDeniedEvent;
use App\Event\AdvertValidatedEvent;
use App\Form\Filter\AdminAdvertType;
use App\Manager\AdvertManager;
use App\Model\Admin\AdvertSearch;
use App\Repository\AdvertRepository;
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
class AdvertController extends AbstractController
{
    public function __construct(
        private AdvertRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher,
        private AdvertManager $manager
    )
    {
    }

    #[Route(path: '/adverts', name: 'app_admin_advert_index')]
    public function index(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getAdmins($search);

        $adverts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $adverts,
            'searchForm' => $form->createView(),
            'type' => '1'
        ]);
    }

    #[Route(path: '/adverts/validated', name: 'app_admin_advert_validated_index')]
    public function validate(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getValidateAdmins($search);

        $adverts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $adverts,
            'searchForm' => $form->createView(),
            'type' => '2'
        ]);
    }

    #[Route(path: '/adverts/refused', name: 'app_admin_advert_refused_index')]
    public function refused(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getDeniedAdmins($search);

        $adverts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $adverts,
            'searchForm' => $form->createView(),
            'type' => '3'
        ]);
    }

    #[Route(path: '/adverts/archive', name: 'app_admin_advert_archive_index')]
    public function archive(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getArchiveAdmins($search);

        $adverts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $adverts,
            'searchForm' => $form->createView(),
            'type' => '4'
        ]);
    }

    #[Route(path: '/adverts/remove', name: 'app_admin_advert_remove_index')]
    public function removed(Request $request): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);

        $form->handleRequest($request);

        $qb = $this->repository->getRemoveAdmins($search);

        $adverts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $adverts,
            'searchForm' => $form->createView(),
            'type' => '5'
        ]);
    }

    #[Route(path: '/adverts/{id}/show/{type}', name: 'app_admin_advert_show', requirements: ['id' => '\d+'])]
    public function show(Advert $advert, $type): Response
    {
        return $this->render('admin/advert/show.html.twig', [
            'advert' => $advert,
            'type' => $type,
        ]);
    }

    #[Route(path: '/adverts/{id}/user', name: 'app_admin_advert_user', requirements: ['id' => '\d+'])]
    public function user(Request $request, User $user): Response
    {
        $search = new AdvertSearch();

        $form = $this->createForm(AdminAdvertType::class, $search);
        $form->handleRequest($request);

        $qb = $this->repository->getAdminByUser($user, $search);

        $adverts = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $adverts,
            'searchForm' => $form->createView(),
            'user' => $user,
            'type' => '6'
        ]);
    }

    #[Route(path: '/adverts/{id}/validate', name: 'app_admin_advert_validate', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function validated(Request $request, Advert $advert): RedirectResponse|JsonResponse
    {
        $form = $this->validateForm($advert);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->manager->validate($advert);

                $this->repository->flush();

                $this->dispatcher->dispatch(new AdvertValidatedEvent($advert));

                $this->addFlash('success', 'L\'annonce a été valider');
            } else {
                $this->addFlash('error', 'Désolé, l\'annonce n\'a pas pu être valider !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir valider cette annonce ?';

        $render = $this->render('Ui/Modal/_validate.html.twig', [
            'form' => $form->createView(),
            'data' => $advert,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/adverts/bulk/validate', name: 'app_admin_advert_bulk_validate', options: ['expose' => true])]
    public function validatedBulk(Request $request): RedirectResponse|JsonResponse
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data'))
            $request->getSession()->set('data', $ids);

        $form = $this->validateMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $advert = $this->repository->find($id);

                    $this->manager->validate($advert);

                    $this->repository->flush();

                    $this->dispatcher->dispatch(new AdvertValidatedEvent($advert));
                }

                $this->addFlash('success', 'Les annonces ont été valider');
            } else {
                $this->addFlash('error', 'Désolé, les annonces n\'ont pas pu être valider !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir valider ces '.count($ids).' annonces ?';
        else
            $message = 'Être vous sur de vouloir valider cette annonce ?';

        $render = $this->render('Ui/Modal/_validate_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/adverts/{id}/denied', name: 'app_admin_advert_denied', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function denied(Request $request, Advert $advert): RedirectResponse|JsonResponse
    {
        $form = $this->deniedForm($advert);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->manager->denied($advert);

                $this->repository->flush();

                $this->dispatcher->dispatch(new AdvertDeniedEvent($advert));

                $this->addFlash('success', 'L\'annonce a été refuser');
            } else {
                $this->addFlash('error', 'Désolé, l\'annonce n\'a pas pu être refuser !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir refuser cette annonce ?';

        $render = $this->render('ui/Modal/_denied.html.twig', [
            'form' => $form->createView(),
            'data' => $advert,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/adverts/bulk/denied', name: 'app_admin_advert_bulk_denied', options: ['expose' => true])]
    public function deniedBulk(Request $request): RedirectResponse|JsonResponse
    {
        $ids = (array) json_decode($request->query->get('data'));

        if ($request->query->has('data'))
            $request->getSession()->set('data', $ids);

        $form = $this->deniedMultiForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $ids = $request->getSession()->get('data');
                $request->getSession()->remove('data');

                foreach ($ids as $id) {
                    $advert = $this->repository->find($id);

                    $this->manager->denied($advert);

                    $this->repository->flush();

                    $this->dispatcher->dispatch(new AdvertDeniedEvent($advert));
                }

                $this->addFlash('success', 'Les annonces ont été refuser');
            } else {
                $this->addFlash('error', 'Désolé, les annonces n\'ont pas pu être refuser !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir refuser ces '.count($ids).' annonces ?';
        else
            $message = 'Être vous sur de vouloir refuser cette annonce ?';

        $render = $this->render('ui/Modal/_denied_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/adverts/{id}/delete', name: 'app_admin_advert_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Advert $advert): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($advert);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($advert);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($advert, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'L\'annonce a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'annonce n\'a pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette annonce ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $advert,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/adverts/bulk/delete', name: 'app_admin_advert_bulk_delete', options: ['expose' => true])]
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
                    $advert = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($advert), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($advert, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les annonces ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les annonces n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' annonces ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette annonce ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function validateForm(Advert $advert): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_validate', ['id' => $advert->getId()]))
            ->getForm();
    }

    private function validateMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_bulk_validate'))
            ->getForm();
    }

    private function deniedForm(Advert $advert): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_denied', ['id' => $advert->getId()]))
            ->getForm();
    }

    private function deniedMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_bulk_denied'))
            ->getForm();
    }

    private function deleteForm(Advert $advert): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_delete', ['id' => $advert->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_advert_bulk_delete'))
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
                ],
                'validate' => [
                    'type' => 'modal-success',
                    'icon' => 'fas fa-reply',
                    'yes_class' => 'btn-outline-success',
                    'no_class' => 'btn-success'
                ],
                'denied' => [
                    'type' => 'modal-amber',
                    'icon' => 'fas fa-share',
                    'yes_class' => 'btn-outline-amber',
                    'no_class' => 'btn-amber'
                ]
            ]
        ];
    }
}
