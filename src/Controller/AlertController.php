<?php

namespace App\Controller;

use App\Event\AlertEvent;
use App\Manager\AlertManager;
use App\Repository\AlertRepository;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AlertController extends AbstractController
{
    public function __construct(
        private AlertManager $manager,
        private AlertRepository $repository,
        private CategoryRepository $categoryRepository,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/alert/create', name: 'app_alert_create', options: ['expose' => true])]
    public function create(Request $request): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->createNotFoundException('Bad request');
        }

        $category = $this->categoryRepository->getEnabledBySlug($request->query->get('category'));
        $subCategory = $this->categoryRepository->getEnabledBySlug($request->query->get('subCategory'));

        if ($this->manager->hasAlert($category, $subCategory)) {
            return new JsonResponse(['success' => false, 'message' => 'Vous avez deja crée une alerte dans cette catégorie.']);
        }

        $alert = $this->manager->createAlert($category, $subCategory);

        $this->repository->add($alert, true);

        $this->dispatcher->dispatch(new AlertEvent($alert));

        return new JsonResponse(['success' => true, 'message' => 'Félicitations ! Votre alerte a été activée. Vous devriez bientôt commencer à recevoir des courriels.',]);
    }
}
