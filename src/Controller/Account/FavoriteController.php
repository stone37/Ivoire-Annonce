<?php

namespace App\Controller\Account;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Settings;
use App\Manager\SettingsManager;
use App\Repository\FavoriteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class FavoriteController extends AbstractController
{
    use ControllerTrait;

    private ?Settings $settings;

    public function __construct(
        private FavoriteRepository $repository,
        private PaginatorInterface $paginator,
        SettingsManager $manager
    )
    {
        $this->settings = $manager->get();
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/favoris', name: 'app_user_favorite_index')]
    public function index(Request $request): NotFoundHttpException|Response
    {
        if (!$this->settings->isActiveAdFavorite()) {
            return $this->createNotFoundException('Bad request');
        }

        $qb = $this->repository->getByUser($this->getUserOrThrow());

        $favorites = $this->paginator->paginate($qb, $request->query->getInt('page', 1), $this->settings->getNumberUserAdvertFavoritePerPage());

        return $this->render('user/favorite/index.html.twig', [
            'favorites' => $favorites,
        ]);
    }
}
