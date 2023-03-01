<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Service\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class NotificationController extends AbstractController
{
    use ControllerTrait;

    private const ROLE = 'ROLE_ADMIN';

    public function __construct(private NotificationService $service)
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/notifications', name: 'app_notification_index')]
    public function index(): Response
    {
        $nbUnread = $this->service->nbUnread($this->getUserOrThrow());

        if (in_array(self::ROLE, $this->getUserOrThrow()->getRoles())) {
            $notifications = $this->service->forChannel(['admin']);
        } else {
            $notifications = $this->service->forUser($this->getUserOrThrow());
        }

        return $this->render('site/notification/index.html.twig', [
            'notifications' => $notifications,
            'nbUnread' => $nbUnread
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/notifications/read', name: 'app_notification_read', options: ['expose' => true])]
    public function readAll(): JsonResponse
    {
        $this->service->readAll($this->getUserOrThrow());

        return new JsonResponse();
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/notifications/unread', name: 'app_notification_unread', options: ['expose' => true])]
    public function nbUnread(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Resource introuvable');
        }

        $notifications = $this->service->unreadForUser($this->getUserOrThrow());

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer(
            null,
            null,
            null,
            null,
            null,
            null, [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {return $object->getId();}
        ]);

        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [$encoder]);
        $response = $serializer->serialize($notifications, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => []]);

        return new JsonResponse($response);
    }

    #[Route(path: '/notifications/get', name: 'app_notifications_get', options: ['expose' => true])]
    public function ajax(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Resource introuvable');
        }

        $notifications = $this->service->forUser($this->getUserOrThrow());

        $render = $this->render('site/menu/_notificationModal.html.twig', ['notifications' => $notifications]);

        $response['html'] = $render->getContent();

        return new JsonResponse($response);
    }
}
