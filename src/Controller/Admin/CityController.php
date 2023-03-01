<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Event\AdminCRUDEvent;
use App\Form\CityType;
use App\Form\Filter\AdminCityType;
use App\Model\Admin\CitySearch;
use App\Repository\CityRepository;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class CityController extends AbstractController
{
    public function __construct(
        private CityRepository $repository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[Route(path: '/cities', name: 'app_admin_city_index')]
    public function index(Request $request): Response
    {
        $search = new CitySearch();
        $form = $this->createForm(AdminCityType::class, $search);

        $form->handleRequest($request);
        $qb = $this->repository->getAdmins($search);

        $cities = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/city/index.html.twig', [
            'cities' => $cities,
            'searchForm' => $form->createView()
        ]);
    }

    #[Route(path: '/cities/create', name: 'app_admin_city_create')]
    public function create(Request $request): RedirectResponse|Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($city);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($city, true);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Une ville a été crée');

            return $this->redirectToRoute('app_admin_city_index');
        }

        return $this->render('admin/city/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/cities/{id}/edit', name: 'app_admin_city_edit', requirements: ['id' => '\d+'])]
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($city);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_EDIT);

            $this->repository->flush();

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_EDIT);

            $this->addFlash('success', 'Une ville a été mise à jour');

            return $this->redirectToRoute('app_admin_city_index');
        }

        return $this->render('admin/city/edit.html.twig', [
            'form' => $form->createView(),
            'city' => $city,
        ]);
    }

    #[Route(path: '/cities/{id}/move', name: 'app_admin_city_move', requirements: ['id' => '\d+'])]
    public function move(Request $request, City $city): RedirectResponse
    {
        if ($request->query->has('pos')) {
            $pos = ($city->getPosition() + (int)$request->query->get('pos'));

            if ($pos >= 0) {
                $city->setPosition($pos);
                $this->repository->flush();

                $this->addFlash('success', 'La position a été modifier');
            }
        }

        return $this->redirectToRoute('app_admin_city_index');
    }

    #[Route(path: '/cities/{id}/delete', name: 'app_admin_city_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, City $city): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($city);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($city);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($city, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'La ville a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, la ville n\'a pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette ville ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $city,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/cities/bulk/delete', name: 'app_admin_city_bulk_delete', options: ['expose' => true])]
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
                    $city = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($city), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($city, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les villes ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les villes n\'ont pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' villes ?';
        else
            $message = 'Être vous sur de vouloir supprimer cette ville ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(City $city): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_city_delete', ['id' => $city->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_city_bulk_delete'))
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


