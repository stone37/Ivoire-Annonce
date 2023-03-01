<?php

namespace App\Controller;

use App\Data\AdvertSearchRequestData;
use App\Entity\Category;
use App\Form\AdvertSearchRequestDataType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    public function __construct(private CategoryRepository $repository)
    {
    }

    #[Route(path: '/adverts/search/form', name: 'app_advert_search_form')]
    public function index(Request $request, ?Category $category): Response
    {
        $data = new AdvertSearchRequestData();
        $data->data = $request->query->get('data');

        $form = $this->createForm(AdvertSearchRequestDataType::class, $data, [
            'action' => $this->generateUrl('app_advert_search_form'),
            'category' => $category
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($request->request->has('category')) {

                $category = $this->repository->find($request->request->get('category'));

                return $category->getParent() ? $this->redirectToRoute('app_advert_index_s', [
                    'category_slug' => $category->getParent()->getSlug(),
                    'sub_category_slug' => $category->getSlug(),
                    'data' => $data->data
                ]) : $this->redirectToRoute('app_advert_index', [
                    'category_slug' => $category->getSlug(),
                    'data' => $data->data
                ]);
            }

            return $this->redirectToRoute('app_advert_search_result', ['data' => $data->data]);
        }

        return $this->render('site/search/index.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }
}