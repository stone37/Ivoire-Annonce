<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdvertSimilarController extends AbstractController
{
    public function __construct(
        private AdvertRepository $repository
    )
    {
    }

    public function index(Advert $advert): Response
    {
        return $this->render('site/advert/similar/index.html.twig', [
            'advert' => $advert,
            'adverts' => $this->repository->getSimilar($advert)
        ]);
    }
}
