<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use App\Event\AdminCRUDEvent;
use App\Form\ReviewAdminType;
use App\Repository\ReviewRepository;
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
class ReviewController extends AbstractController
{
    public function __construct(
        private ReviewRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/reviews', name: 'app_admin_review_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->findBy([], ['createdAt' => 'desc']);

        $reviews = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/review/index.html.twig', [
            'reviews' => $reviews
        ]);
    }

    #[Route(path: '/reviews/{id}/edit', name: 'app_admin_review_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, Review $review): RedirectResponse|Response
    {
        $form = $this->createForm(ReviewAdminType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($review);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Un avis a été mise à jour');

            return $this->redirectToRoute('app_admin_review_index');
        }

        return $this->render('admin/review/edit.html.twig', [
            'form' => $form->createView(),
            'review' => $review,
        ]);
    }

    #[Route(path: '/reviews/{id}/show', name: 'app_admin_review_show', requirements: ['id' => '\d+'])]
    public function show(Review $review): Response
    {
        return $this->render('admin/review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route(path: '/reviews/{id}/delete', name: 'app_admin_review_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Review $review): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($review);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($review);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($review, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);


                $this->addFlash('success', 'L\'avis a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'avis n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cet avis ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $review,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/reviews/bulk/delete', name: 'app_admin_review_bulk_delete', options: ['expose' => true])]
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
                    $review = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($review), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($review, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les avis a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les avis n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' avis ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet avis ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Review $review): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_review_delete', ['id' => $review->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_review_bulk_delete'))
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


