<?php

namespace App\Controller\Admin;

use App\Entity\Emailing;
use App\Event\AdminCRUDEvent;
use App\Form\EmailingSenderType;
use App\Form\EmailingType;
use App\Repository\EmailingRepository;
use App\Repository\NewsletterDataRepository;
use App\Repository\UserRepository;
use App\Service\NewsletterService;
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
class EmailingController extends AbstractController
{
    public function __construct(
        private EmailingRepository $repository,
        private UserRepository $userRepository,
        private NewsletterDataRepository $newsletterDataRepository,
        private PaginatorInterface $paginator,
        private EventDispatcherInterface $dispatcher,
        private NewsletterService $service
    )
    {
    }

    #[Route(path: '/emailing/{type}/list', name: 'app_admin_emailing_index', requirements: ['type' => '\d+'])]
    public function index(Request $request, int $type): Response
    {
        $qb = $this->repository->getAdmins($type);

        $emailings = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/emailing/index.html.twig', [
            'emailings' => $emailings,
            'type' => $type,
        ]);
    }

    #[Route(path: '/emailing/create', name: 'app_admin_emailing_create')]
    public function create(Request $request): RedirectResponse|Response
    {
        $emailing = new Emailing();

        $form = $this->createForm(EmailingSenderType::class, $emailing);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $event = new AdminCRUDEvent($emailing);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_CREATE);

            $this->repository->add($emailing, true);

            $this->service->sendEmailing($emailing);

            $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_CREATE);

            $this->addFlash('success', 'Votre email a été envoyé');

            return $this->redirectToRoute('app_admin_emailing_index', ['type' => '1']);
        }

        return $this->render('admin/emailing/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/emailing/{id}/resend/{type}', name: 'app_admin_emailing_resend', requirements: ['id' => '\d+'])]
    public function resend(Request $request, Emailing $emailing, string $type): RedirectResponse|Response
    {
        if ($emailing->getGroupe() == Emailing::GROUP_PARTICULIER) {
            $form = $this->createForm(EmailingSenderType::class, $emailing);
        } else {
            $form = $this->createForm(EmailingType::class, $emailing);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($emailing->getGroupe() == Emailing::GROUP_PARTICULIER) {

                $this->repository->flush();

                $this->service->sendEmailing($emailing);
                $this->addFlash('success', 'Votre email a été envoyé');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => 1]);

            } elseif ($emailing->getGroupe() == Emailing::GROUP_USER) {
                $users = $this->userRepository->findAllUsers();

                if (empty($users)) {
                    $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                    return $this->redirectToRoute('app_admin_emailing_index', ['type' => 2]);
                }

                $this->repository->flush();
                $this->service->sendUserEmailing($emailing, $users);
                $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => 2]);
            } elseif ($emailing->getGroupe() == Emailing::GROUP_USER_PRO) {
                $users = $this->userRepository->findAllProUsers();

                if (empty($users)) {
                    $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                    return $this->redirectToRoute('app_admin_emailing_index', ['type' => 3]);
                }

                $this->repository->flush();
                $this->service->sendProUserEmailing($emailing, $users);
                $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => 3]);
            } else {
                $newsletters = $this->newsletterDataRepository->findAll();

                if (empty($newsletters)) {
                    $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                    return $this->redirectToRoute('app_admin_emailing_index', ['type' => 4]);
                }

                $this->repository->flush();

                $this->service->sendNewsletterEmailing($emailing, $newsletters);

                $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => 4]);
            }
        }

        return $this->render('admin/emailing/edit.html.twig', [
            'form' => $form->createView(),
            'emailing' => $emailing,
            'type' => $type,
        ]);
    }

    #[Route(path: '/emailing/create/user', name: 'app_admin_emailing_user')]
    public function user(Request $request): Response
    {
        $users = $this->userRepository->findAllUsers();

        $emailing = (new Emailing())->setGroupe(Emailing::GROUP_USER);

        $form = $this->createForm(EmailingType::class, $emailing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (empty($users)) {
                $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => 3]);
            }

            $this->repository->add($emailing, true);

            $this->service->sendUserEmailing($emailing, $users);

            $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

            return $this->redirectToRoute('app_admin_emailing_index', ['type' => '3']);
        }

        return $this->render('admin/emailing/user.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route(path: '/emailing/create/user-pro', name: 'app_admin_emailing_user_pro')]
    public function userPro(Request $request): Response
    {
        $users = $this->userRepository->findAllProUsers();

        $emailing = (new Emailing())->setGroupe(Emailing::GROUP_USER_PRO);

        $form = $this->createForm(EmailingType::class, $emailing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (empty($users)) {
                $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => '2']);
            }

            $this->repository->add($emailing, true);

            $this->service->sendProUserEmailing($emailing, $users);

            $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

            return $this->redirectToRoute('app_admin_emailing_index', ['type' => '2']);
        }

        return $this->render('admin/emailing/user-pro.html.twig', [
            'form' => $form->createView(),
            'users' => $users,
        ]);
    }

    #[Route(path: '/emailing/create/newsletter', name: 'app_admin_emailing_newsletter')]
    public function newsletter(Request $request): RedirectResponse|Response
    {
        $newsletters = $this->newsletterDataRepository->findAll();

        $emailing = (new Emailing())->setGroupe(Emailing::GROUP_NEWSLETTER);

        $form = $this->createForm(EmailingType::class, $emailing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (empty($newsletters)) {
                $this->addFlash('error', 'Votre newsletter n\'a pas pu etre envoyé par manque de destinataire');

                return $this->redirectToRoute('app_admin_emailing_index', ['type' => '4']);
            }

            $this->repository->add($emailing, true);

            $this->service->sendNewsletterEmailing($emailing, $newsletters);

            $this->addFlash('success', 'Votre newsletter a été envoyée avec succès');

            return $this->redirectToRoute('app_admin_emailing_index', ['type' => '4']);
        }

        return $this->render('admin/emailing/newsletter.html.twig', [
            'form' => $form->createView(),
            'newsletters' => $newsletters,
        ]);
    }

    #[Route(path: '/emailing/newsletters', name: 'app_admin_emailing_newsletters')]
    public function newsletters(Request $request): Response
    {
        $qb = $this->newsletterDataRepository->findBy([], ['createdAt' => 'desc']);

        $newsletters = $this->paginator->paginate($qb, $request->query->getInt('page', 1), 25);

        return $this->render('admin/emailing/newsletters.html.twig', [
            'newsletters' => $newsletters,
        ]);
    }

    #[Route(path: '/emailing/{id}/delete', name: 'app_admin_emailing_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Emailing $emailing): RedirectResponse|JsonResponse
    {
        $form = $this->deleteForm($emailing);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $event = new AdminCRUDEvent($emailing);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::PRE_DELETE);

                $this->repository->remove($emailing, true);

                $this->dispatcher->dispatch($event, AdminCRUDEvent::POST_DELETE);

                $this->addFlash('success', 'Le mail a été supprimer !');
            } else {
                $this->addFlash('error', 'Désolé, le mail n\'a pas pu etre supprimer !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cet mail ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $emailing,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    #[Route(path: '/emailing/bulk/delete', name: 'app_admin_emailing_bulk_delete', options: ['expose' => true])]
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
                    $emailing = $this->repository->find($id);
                    $this->dispatcher->dispatch(new AdminCRUDEvent($emailing), AdminCRUDEvent::PRE_DELETE);

                    $this->repository->remove($emailing, false);
                }

                $this->repository->flush();

                $this->addFlash('success', 'Les mails ont été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, les mails n\'ont pas pu être supprimée!');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        if (count($ids) > 1)
            $message = 'Être vous sur de vouloir supprimer ces '.count($ids).' mails ?';
        else
            $message = 'Être vous sur de vouloir supprimer cet mail ?';

        $render = $this->render('Ui/Modal/_delete_multi.html.twig', [
            'form' => $form->createView(),
            'data' => $ids,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Emailing $emailing): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_emailing_delete', ['id' => $emailing->getId()]))
            ->getForm();
    }

    private function deleteMultiForm(): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_admin_emailing_bulk_delete'))
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


