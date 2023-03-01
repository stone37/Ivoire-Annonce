<?php

namespace App\Controller;

use App\Controller\Traits\ControllerTrait;
use App\Repository\CategoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class CategoryController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private CategoryRepository $repository,
        private Breadcrumbs $breadcrumbs,
    )
    {
    }

    public function index(): Response
    {
        return $this->render('site/category/index.html.twig', [
            'categories' => $this->repository->getPartielHasNotParent(6)
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/pa/passe-une-annonce-gratuite', name: 'app_switch_category')]
    public function switch(): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Sélectionner une catégorie');

        return $this->render('site/category/switch.html.twig', [
            'categories' => $this->repository->getHasNotParent()
        ]);
    }
}
