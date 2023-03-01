<?php

namespace App\Controller\Account;

use App\Entity\Settings;
use App\Entity\Thread;
use App\Manager\SettingsManager;
use App\Manager\ThreadManager;
use App\Provider\ThreadProvider;
use App\Service\Composer;
use App\Service\Sender;
use App\Service\ThreadDeleter;
use JetBrains\PhpStorm\ArrayShape;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/u')]
class MessageController extends AbstractController
{
    private ?Settings $settings;

    public function __construct(
        private ThreadProvider $provider,
        private ThreadManager $manager,
        private Composer $composer,
        private Sender $sender,
        private ThreadDeleter $deleter,
        private ValidatorInterface $validator,
        SettingsManager $settingsManager
    )
    {
        $this->settings = $settingsManager->get();
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/messages', name: 'app_user_thread_index')]
    public function index(): NotFoundHttpException|Response
    {
        if (!$this->settings->isActiveThread()) {
            return $this->createNotFoundException('Bad request');
        }

        return $this->render('user/message/index.html.twig', [
            'threads' => $this->provider->getThreads()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/messages/{id}', name: 'app_user_thread_show', requirements: ['id' => '\d+'])]
    public function show(Request $request, int $id): NotFoundHttpException|Response
    {
        if (!$this->settings->isActiveThread()) {
            return $this->createNotFoundException('Bad request');
        }

        return $this->render('user/message/show.html.twig', [
            'thread' => $this->provider->getThread($id),
            'index' => $request->query->get('index')
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/messages/{id}/reply', name: 'app_user_thread_reply', requirements: ['id' => '\d+'], options: ['expose' => true], methods: ['GET', 'POST'])]
    public function replyThread(Request $request, Thread $thread): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return  $this->createNotFoundException('Bad request');
        }

        $message = $this->composer->reply($thread)
            ->setSender($this->getUser())
            ->setBody($request->request->get('content'))
            ->getMessage();

        $errors = $this->validator->validate($message);

        if (!$this->isCsrfTokenValid('advert-message', $request->request->get('_token'))) {
            $errors[] = 'Le jeton CSRF est invalide.';
        }

        if (!count($errors)) {
            $this->sender->send($message);

            return new JsonResponse(['success' => true, 'message' => 'Votre message a été envoyer']);
        }

        $data = [];

        foreach ($errors as $error) {
            $data[] = $error->getMessage();
        }

        return new JsonResponse(['success' => false, 'errors' => json_encode($data)]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/messages/{id}/delete', name: 'app_user_thread_delete', requirements: ['id' => '\d+'], options: ['expose' => true], methods: ['GET', 'POST'])]
    public function delete(Request $request, int $id): NotFoundHttpException|RedirectResponse|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->createNotFoundException('Bad request');
        }

        $thread = $this->provider->getThread($id);

        $form = $this->deleteForm($thread);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $this->deleter->markAsDeleted($thread);
                $this->manager->saveThread($thread);

                $this->addFlash('success', 'La conversation a été supprimée');
            } else {
                $this->addFlash('error', 'Désolé, la conversation n\'a pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette conversation ?';

        $render = $this->render('Ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $thread,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Thread $thread): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_user_thread_delete', ['id' => $thread->getId()]))
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