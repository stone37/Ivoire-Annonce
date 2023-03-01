<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Alert;
use App\Entity\Settings;
use App\Manager\SettingsManager;
use App\Repository\AlertRepository;
use JetBrains\PhpStorm\ArrayShape;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class AlertController extends AbstractController
{
    use ControllerTrait;

    private ?Settings $settings;

    public function __construct(
        private AlertRepository $repository,
        private PaginatorInterface $paginator,
        SettingsManager $manager
    )
    {
        $this->settings = $manager->get();
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/alertes', name: 'app_user_alert_index', methods: ['GET'])]
    public function index(Request $request): NotFoundHttpException|Response
    {
        if (!$this->settings->isActiveAlert()) {
            return $this->createNotFoundException('Bad request');
        }

        $user = $this->getUserOrThrow();
        $alerts = $this->repository->getByUser($user);
        $alerts = $this->paginator->paginate($alerts, $request->query->getInt('page', 1), 20);

        return $this->render('user/alert/index.html.twig', [
            'alerts' => $alerts
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/alertes/enabled', name: 'app_user_alert_enabled_index', methods: ['GET'])]
    public function enabled(Request $request): NotFoundHttpException|Response
    {
        if (!$this->settings->isActiveAlert()) {
            return $this->createNotFoundException('Bad request');
        }

        $user = $this->getUserOrThrow();
        $alerts = $this->repository->getEnabledByUser($user);
        $alerts = $this->paginator->paginate($alerts, $request->query->getInt('page', 1), 20);

        return $this->render('user/alert/enabled.html.twig', [
            'alerts' => $alerts
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/alertes/{id}/change/state', name: 'app_user_alert_change_state', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function changeState(Alert $alert): RedirectResponse
    {
        if ($alert->isEnabled()) {
            $alert->setEnabled(false);
            $this->repository->flush();

            $this->addFlash('success', 'Votre alert est maintenant inactive');

            return $this->redirectToRoute('app_user_alert_enabled_index');
        } else {
            $alert->setEnabled(true);
            $this->repository->flush();

            $this->addFlash('success', 'Votre alert est maintenant active');

           return $this->redirectToRoute('app_user_alert_index');
        }
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/alertes/{id}/delete', name: 'app_user_alert_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Alert $alert): RedirectResponse|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Bad request');
        }

        $form = $this->deleteForm($alert);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->repository->remove($alert, true);

                $this->addFlash('success', 'L\'alerte a été supprimé');
            } else {
                $this->addFlash('error', 'Désolé, l\'alerte n\'a pas pu être supprimée !');
            }

            $url = $request->request->get('referer');

            return new RedirectResponse($url);
        }

        $message = 'Être vous sur de vouloir supprimer cette alerte ?';

        $render = $this->render('ui/Modal/_delete.html.twig', [
            'form' => $form->createView(),
            'data' => $alert,
            'message' => $message,
            'configuration' => $this->configuration(),
        ]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }

    private function deleteForm(Alert $alert): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('app_user_alert_delete', ['id' => $alert->getId()]))
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