<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Entity\Advert;
use App\Entity\Favorite;
use App\Event\FavoriteCreateEvent;
use App\Event\FavoriteDeleteEvent;
use App\Repository\FavoriteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/favorite')]
class FavoriteController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private FavoriteRepository $repository,
        private EventDispatcherInterface $dispatcher
    )
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/{id}/add', name: 'app_favorite_add', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function add(Request $request, Advert $advert): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Invalid Request');
        }

        $user = $this->getUserOrThrow();
        $favorite = $this->repository->findOneBy(['advert' => $advert, 'owner' => $user]);

        if ($favorite) {
            return $this->json(['code' => Response::HTTP_UNPROCESSABLE_ENTITY, 'message' => 'Cette annonce est déjà dans vos favoris'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $favorite = (new Favorite())
            ->setAdvert($advert)
            ->setOwner($user);

        $this->repository->add($favorite, true);

        $this->dispatcher->dispatch(new FavoriteCreateEvent($favorite));

        return $this->json(['code' => Response::HTTP_OK, 'message' => 'L\'annonce a été ajouter à vos favoris'], Response::HTTP_OK);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/{id}/delete', name: 'app_favorite_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, Advert $advert): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Invalid Request');
        }

        $user = $this->getUserOrThrow();
        $favorite = $this->repository->findOneBy(['advert' => $advert, 'owner' => $user]);

        if (!$favorite) {
            return $this->json(['code' => Response::HTTP_UNPROCESSABLE_ENTITY, 'message' => 'Une erreur est survenu, le favoris n\'existe pas'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->repository->remove($favorite, true);

        $this->dispatcher->dispatch(new FavoriteDeleteEvent($favorite));

        return $this->json(['code' => Response::HTTP_OK, 'message' => 'L\'annonce a été retirer de vos favoris'], Response::HTTP_OK);
    }
}

