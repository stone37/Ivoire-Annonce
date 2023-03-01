<?php

namespace App\Controller\Admin;

use App\Entity\Report;
use App\Event\AdminCRUDEvent;
use App\Repository\ReportRepository;
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
class ReportController extends AbstractController
{
    public function __construct(
        private ReportRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/reports', name: 'app_admin_report_index')]
    public function index(Request $request): Response
    {
        $qb = $this->repository->findBy([], ['createdAt' => 'desc']);

        $reports = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/report/index.html.twig', [
            'reports' => $reports
        ]);
    }

    #[Route(path: '/reports/{id}/show', name: 'app_admin_report_show', requirements: ['id' => '\d+'])]
    public function show(Report $report): Response
    {
        return $this->render('admin/report/show.html.twig', [
            'report' => $report
        ]);
    }

    #[Route(path: '/reports/{id}/delete', name: 'app_admin_report_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Report $report): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($report);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($report);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($report, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);


                $this->addFlash('success', 'Le signalement a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, le signalement n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cet signalement ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $report,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/reports/bulk/delete', name: 'app_admin_report_bulk_delete', options: ['expose' => true])]
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
                    $report = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($report), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($report, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les signalements a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les signalements n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' signalements ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet signalement ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration()
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Report $report): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_report_delete', ['id' => $report->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_report_bulk_delete'))
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


