<?php

namespace App\Controller;

use App\Repository\CategoryPremiumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CategoryPremiumController extends AbstractController
{
    public function __construct(private CategoryPremiumRepository $repository)
    {
    }

    public function index(): Response
    {
        return $this->render('site/categoryPremium/index.html.twig', [
            'premiums' => $this->repository->getEnabled()
        ]);
    }
}
