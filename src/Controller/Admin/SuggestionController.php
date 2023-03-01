<?php

namespace App\Controller\Admin;

use App\Entity\Suggestion;
use App\Event\AdminCRUDEvent;
use App\Repository\SuggestionRepository;
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
class SuggestionController extends AbstractController
{
    public function __construct(
        private SuggestionRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/suggestions', name: 'app_admin_suggestion_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->findBy([], ['createdAt' => 'desc']);

        $suggestions = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/suggestion/index.html.twig', [
            'suggestions' => $suggestions
        ]);
    }

    #[Route(path: '/suggestions/{id}/show', name: 'app_admin_suggestion_show', requirements: ['id' => '\d+'])]
    public function show(Suggestion $suggestion): Response
    {
        return $this->render('admin/suggestion/show.html.twig', [
            'suggestion' => $suggestion
        ]);
    }

    #[Route(path: '/suggestions/{id}/delete', name: 'app_admin_suggestion_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Suggestion $suggestion): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($suggestion);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($suggestion);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($suggestion, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);


                $this->addFlash('success', 'La suggestion a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la suggestion n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette suggestion ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $suggestion,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/suggestions/bulk/delete', name: 'app_admin_suggestion_bulk_delete', options: ['expose' => true])]
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
                    $suggestion = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($suggestion), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($suggestion, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les suggestions a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les suggestions n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' suggestions ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette suggestions ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Suggestion $suggestion): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_suggestion_delete', ['id' => $suggestion->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_suggestion_bulk_delete'))
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


