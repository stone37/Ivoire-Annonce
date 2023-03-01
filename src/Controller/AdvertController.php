<?php

namespace App\Controller;

use App\Controller\RequestDataHandler\AdvertHybrideListDataHandler;
use App\Controller\RequestDataHandler\AdvertListDataHandler;
use App\Controller\RequestDataHandler\AdvertSortDataHandler;
use App\Controller\RequestDataHandler\PaginationDataHandler;
use App\Controller\Traits\ControllerTrait;
use App\Entity\Advert;
use App\Entity\Category;
use App\Event\AdvertBadEvent;
use App\Event\AdvertCreateEvent;
use App\Event\AdvertInitEvent;
use App\Event\AdvertListingEvent;
use App\Event\AdvertPreCreateEvent;
use App\Event\AdvertViewEvent;
use App\Exception\CategoryNotFoundException;
use App\Form\Filter\AdvertHybrideFilterType;
use App\Manager\AdvertManager;
use App\Model\AdvertHybrideSearch;
use App\Repository\AdvertRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class AdvertController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private AdvertRepository $repository,
        private AdvertManager $manager,
        private EventDispatcherInterface $dispatcher,
        private Breadcrumbs $breadcrumbs,
        private PaginatorInterface $paginator,
        private AdvertListDataHandler $advertListDataHandler,
        private AdvertHybrideListDataHandler $advertHybrideListDataHandler,
        private AdvertSortDataHandler $advertSortDataHandler,
        private PaginationDataHandler $paginationDataHandler
    )
    {
    }

    #[Route(path: '/va/{category_slug}', name: 'app_advert_index', options: ['expose' => true])]
    #[Route(path: '/va/{category_slug}/{sub_category_slug}', name: 'app_advert_index_s', options: ['expose' => true])]
    public function index(Request $request): Response
    {
        $category = $this->manager->getMainCategory();

        if (null === $category) {
            throw new CategoryNotFoundException();
        }

        $this->breadcrumbs($category);

        $this->dispatcher->dispatch(new AdvertListingEvent($request));

        $search = $this->manager->filterEntity();
        $form = $this->createForm($this->manager->createFilterForm(), $search, ['category' => $category]);
        $form->handleRequest($request);

        $requestData = array_merge($search->toArray(), $request->query->all());

        if ($form->isSubmitted() &&
            !$form->isValid() &&
            !$request->query->has('order_by') &&
            !$request->query->has('sort') &&
            !$request->query->has('limit')) {
            $requestData = $this->clearInvalidEntries($form, $requestData);
        }

        $data = array_merge(
            $this->advertListDataHandler->retrieveData($requestData),
            $this->advertSortDataHandler->retrieveData($requestData),
            $this->paginationDataHandler->retrieveData($requestData)
        );

        $adverts = $this->paginator->paginate(
            $this->repository->getAdvertLists($data),
            $request->query->getInt('page', $data[PaginationDataHandler::PAGE_INDEX]),
            $data[PaginationDataHandler::LIMIT_INDEX]);

        return $this->render('site/advert/index.html.twig', [
            'adverts' => $adverts,
            'view' => $this->manager->showFilterViewRoute(),
            'category' => $category,
            'form' => $form->createView(),
            'search' => $search
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/pa/{category_slug}/create', name: 'app_advert_create', options: ['expose' => true])]
    public function create(Request $request): RedirectResponse|Response
    {
        $user = $this->getUserOrThrow();
        $advert = $this->manager->createAdvert($user);

        $this->dispatcher->dispatch(new AdvertInitEvent($request));

        $form = $this->createForm($this->manager->createForm(), $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->dispatcher->dispatch(new AdvertPreCreateEvent($advert, $request));

            $this->repository->add($advert, true);

            $this->addFlash('success', 'Votre annonce a été crée. Il sera valider dans quelque instant.');

            $event = $this->dispatcher->dispatch(new AdvertCreateEvent($advert));

            if (null === $response = $event->getResponse()) {
                $response = new RedirectResponse($this->generateUrl('app_home'));
            }

            return $response;
        } else {
            $this->dispatcher->dispatch(new AdvertBadEvent($advert, $request));
        }

        return $this->render('site/advert/create.html.twig', [
            'form' => $form->createView(),
            'view' => $this->manager->viewRoute(),
            'advert' => $advert
        ]);
    }

    #[Route(path: '/a/{category_slug}/{city}/{reference}/{slug}', name: 'app_advert_show')]
    #[Route(path: '/a/{category_slug}/{sub_category_slug}/{city}/{reference}/{slug}', name: 'app_advert_show_s')]
    public function show(string $slug): Response
    {
        $advert = $this->repository->getEnabledBySlug($slug);
        $advert_number = $this->repository->getValidatedByUserNumber($advert->getOwner());
        $this->showBreadcrumbs($advert);

        $this->dispatcher->dispatch(new AdvertViewEvent($advert));

        return $this->render('site/advert/show.html.twig', [
            'advert' => $advert,
            'advert_number' => $advert_number
        ]);
    }

    #[Route(path: '/a/search/results', name: 'app_advert_search_result', options: ['expose' => true])]
    public function searchResult(Request $request): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Résultat de recherche d\'annonce');

        $search = new AdvertHybrideSearch();
        $form = $this->createForm(AdvertHybrideFilterType::class, $search);

        $form->handleRequest($request);

        $requestData = array_merge($search->toArray(), $request->query->all());

        if ($form->isSubmitted() &&
            !$form->isValid() &&
            !$request->query->has('order_by') &&
            !$request->query->has('sort') &&
            !$request->query->has('limit')) {
            $requestData = $this->clearInvalidEntries($form, $requestData);
        }

        $data = array_merge(
            $this->advertHybrideListDataHandler->retrieveData($requestData),
            $this->advertSortDataHandler->retrieveData($requestData),
            $this->paginationDataHandler->retrieveData($requestData)
        );

        $adverts = $this->paginator->paginate(
            $this->repository->getAdvertHybrideLists($data),
            $request->query->getInt('page', $data[PaginationDataHandler::PAGE_INDEX]),
            $data[PaginationDataHandler::LIMIT_INDEX]);

        return $this->render('site/advert/searchResult.html.twig', [
            'adverts' => $adverts,
            'form' => $form->createView(),
            'search' => $search
        ]);
    }

    #[Route(path: '/a/search/by-query', name: 'app_advert_search_by_query', options: ['expose' => true])]
    public function search(Request $request): NotFoundHttpException|JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->createNotFoundException('Bad request');
        }
        $query = $request->request->get('q');
        $adverts = $this->repository->searchByQuery($query);

        $response = $this->responseFormatter($adverts, $query);

        $encoder = new JsonEncoder();
        $defaultContext = [AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {return $object->getId();}];
        $normalizer = new ObjectNormalizer(null, null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([$normalizer], [$encoder]);
        $response = $serializer->serialize($response, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => []]);

        return new JsonResponse($response);
    }

    private function breadcrumbs(Category $category): Breadcrumbs
    {
        $this->breadcrumb($this->breadcrumbs);

        $categories = [];

        do {
            $categories[] = $category;
            $category = $category->getParent() ? $category->getParent() : null;
        } while($category);

        $number = count($categories)-1;

        for ($i = $number; $i >= 0; $i--) {
            if ($number == $i) {
                $this->breadcrumbs->addItem(ucfirst($categories[$i]->getName()), $this->generateUrl('app_advert_index', ['category_slug' => $categories[$i]->getSlug()]));
            } else {
                $this->breadcrumbs->addItem(ucfirst($categories[$i]->getName()), $this->generateUrl('app_advert_index_s', ['category_slug' => $categories[count($categories)-1]->getSlug(), 'sub_category_slug' => $categories[$i]->getSlug()]));
            }
        }

        return $this->breadcrumbs;
    }

    private function clearInvalidEntries(FormInterface $form, array $requestData): array
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($form->getErrors(true, true) as $error) {
            $errorOrigin = $error->getOrigin();
            $propertyAccessor->setValue(
                $requestData,
                ($errorOrigin->getParent()->getPropertyPath() ?? '') . $errorOrigin->getPropertyPath(),
                ''
            );
        }

        return $requestData;
    }

    private function showBreadcrumbs(Advert $advert): Breadcrumbs
    {
        $category = $advert->getCategory();
        $subCategory = $advert->getSubCategory();
        $city = $advert->getLocation()->getCity();

        $this->breadcrumbs->addItem('Accueil', $this->generateUrl('app_home'));
        $this->breadcrumbs->addItem(ucfirst($category->getName()), $this->generateUrl('app_advert_index', ['category_slug' => $category->getSlug()]));

        if ($subCategory) {
            $this->breadcrumbs->addItem(ucfirst($subCategory->getName()), $this->generateUrl('app_advert_index_s', ['category_slug' => $category->getSlug(), 'sub_category_slug' => $subCategory->getSlug()]));
            $this->breadcrumbs->addItem(ucfirst($city), $this->generateUrl('app_advert_index_s', ['category_slug' => $category->getSlug(), 'sub_category_slug' => $subCategory->getSlug(), 'city' => $city]));
        } else {
            $this->breadcrumbs->addItem(ucfirst($city), $this->generateUrl('app_advert_index', ['category_slug' => $category->getSlug(), 'city' => $city]));
        }

        $this->breadcrumbs->addItem('Code '.$advert->getReference());

        return $this->breadcrumbs;
    }

    private function responseFormatter(array $adverts, string $query): array
    {
        $response = [];

        /** @var Advert $advert */
        foreach ($adverts as $advert) {
            $category = $advert->getCategory();
            $subCategory = $advert->getSubCategory();

            $marque = $advert->getMarque();
            $brand = $advert->getBrand();
            $title = $advert->getTitle();

            $hasMarque = preg_match("#{$query}#i","' . $marque . '" );
            $hasBrand = preg_match("#{$query}#i","' . $brand . '" );
            $hasTitle = preg_match("#{$query}#i","' . $title . '" );

            if ($subCategory) {
                $route = $this->generateUrl('app_advert_index_s', ['category_slug' => $category->getSlug(), 'sub_category_slug' => $subCategory->getSlug()]);
                $subName = $subCategory->getName();
                $hasSubCategory = preg_match("#{$query}#i","' . $subName . '" );

                if ($hasSubCategory) {
                    $response[] = ['title' => $advert->getSubCategory()->getName(), 'route' => $route];
                } elseif ($hasMarque) {
                    $route = $this->generateUrl('app_advert_index_s', ['category_slug' => $category->getSlug(), 'sub_category_slug' => $subCategory->getSlug(), 'marque' => $marque]);
                    $title = $marque . ' dans <span>' . strtolower($subCategory->getName()) . '</span>';

                    $response[] = ['title' => $title , 'route' => $route];
                } elseif ($hasBrand) {
                    $route = $this->generateUrl('app_advert_index_s', ['category_slug' => $category->getSlug(), 'sub_category_slug' => $subCategory->getSlug(), 'brand' => $brand]);
                    $title = $brand . ' dans <span>' . strtolower($subCategory->getName()) . '</span>';

                    $response[] = ['title' => $title , 'route' => $route];
                } elseif ($hasTitle) {
                    $title = $this->ellipse($title, 20) . ' dans <span>' . strtolower($subCategory->getName()).'</span>';

                    $response[] = ['title' => $title, 'route' => $route];
                } else {
                    $title = $this->ellipse($advert->getDescription(), 20) . ' dans <span>' . strtolower($subCategory->getName()). '</span>';
                    $response[] = ['title' => $title, 'route' => $route];
                }
            } else {
                $route = $this->generateUrl('app_advert_index', ['category_slug' => $category->getSlug()]);

                if ($hasMarque) {
                    $route = $this->generateUrl('app_advert_index', ['category_slug' => $category->getSlug(), 'marque' => $marque]);
                    $title = $marque . ' dans <span>' . strtolower($category->getName()) . '</span>';

                    $response[] = ['title' => $title , 'route' => $route];
                } elseif ($hasBrand) {
                    $route = $this->generateUrl('app_advert_index', ['category_slug' => $category->getSlug(), 'brand' => $brand]);
                    $title = $brand . ' dans <span>' . strtolower($category->getName()) . '</span>';

                    $response[] = ['title' => $title , 'route' => $route];
                } elseif ($hasTitle) {
                    $title = $this->ellipse($title, 20) . ' dans <span>' . strtolower($category->getName()).'</span>';

                    $response[] = ['title' => $title, 'route' => $route];
                } else {
                    $title = $this->ellipse($advert->getDescription(), 20) . ' dans <span>' . strtolower($category->getName()). '</span>';
                    $response[] = ['title' => $title, 'route' => $route];
                }
            }
        }

        return $response;
    }

    private function ellipse(string $str, int $n_chars, string $crop_str = '...'): string
    {
        $buff = strip_tags($str);

        if (strlen($buff) > $n_chars) {
            $cut_index = strpos($buff,' ', $n_chars);
            $buff = substr($buff,0, ($cut_index === false ? $n_chars : $cut_index + 1)) . $crop_str;
        }

        return $buff;
    }
}
