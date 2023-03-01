<?php

namespace App\Controller\Admin;

use App\Entity\Locale;
use App\Event\AdminCRUDEvent;
use App\Form\LocaleType;
use App\Repository\LocaleRepository;
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
class LocaleController extends AbstractController
{
    public function __construct(
        private LocaleRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/locales', name: 'app_admin_locale_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->findBy([], ['createdAt' => 'desc']);

        $locales = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/locale/index.html.twig', [
            'locales' => $locales
        ]);
    }

    #[Route(path: '/locales/create', name: 'app_admin_locale_create')]
    public function create(Request $request): RedirectResponse|Response
    {
        $locale = new Locale();

        $form = $this->createForm(LocaleType::class, $locale);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($locale);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($locale, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Un paramètre régional a été crée');

            return $this->redirectToRoute('app_admin_locale_index');
        }

        return $this->render('admin/locale/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/locales/{id}/edit', name: 'app_admin_locale_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Locale $locale): RedirectResponse|Response
    {
        $form = $this->createForm(LocaleType::class, $locale);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($locale);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un paramètre régional a été mise à jour');

            return $this->redirectToRoute('app_admin_locale_index');
        }

        return $this->render('admin/locale/edit.html.twig', [
            'form' => $form->createView(),
            'locale' => $locale,
        ]);
    }

    #[Route(path: '/locales/{id}/delete', name: 'app_admin_locale_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Locale $locale): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($locale);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($locale);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($locale, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le paramètre régional a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le paramètre régional n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cet paramètre régional ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $locale,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/locales/bulk/delete', name: 'app_admin_locale_bulk_delete', options: ['expose' => true])]
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
                    $locale = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($locale), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($locale, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les paramètres régionaux ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les paramètres régionaux n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' paramètres régionaux ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet paramètre régional ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Locale $locale): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_locale_delete', ['id' => $locale->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_locale_bulk_delete'))
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

