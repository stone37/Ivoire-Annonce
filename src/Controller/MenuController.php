<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository
    )
    {
    }

    public function navbar(Request $request): Response
    {
        return $this->render('site/menu/navbar.html.twig', [
            'categories' => $this->categoryRepository->getHasNotParent(),
            'request' => $request
        ]);
    }

    public function navbarSm(Request $request): Response
    {
        if ($request->attributes->has('sub_category_slug')) {
            $slug = $request->attributes->get('sub_category_slug');
        } else {
            if ($request->attributes->get('category_slug')) {
                $slug = $request->attributes->get('category_slug');
            } else {
                $slug = null;
            }
        }

        return $this->render('site/menu/navbarSm.html.twig', [
            'categories' => $this->categoryRepository->getByParentSlug($slug),
            'request' => $request
        ]);
    }
}
