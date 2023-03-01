<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Storage\AdvertStorage;
use App\Storage\CartStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OptionController extends AbstractController
{
    public function __construct(
        private ProductRepository $repository,
        private CartStorage $cartStorage,
        private AdvertStorage $advertStorage
    )
    {
    }

    public function index(): Response
    {
        $headers = $this->repository->getOptions(Product::CATEGORY_ADVERT_HEAD_OPTION);
        $urgents = $this->repository->getOptions(Product::CATEGORY_ADVERT_URGENT_OPTION);
        $galleries = $this->repository->getOptions(Product::CATEGORY_ADVERT_HOME_GALLERY_OPTION);
        $featured = $this->repository->getOptions(Product::CATEGORY_ADVERT_FEATURED_OPTION);

        return $this->render('site/option/index.html.twig', [
            'headers' => $headers,
            'urgents' => $urgents,
            'galleries' => $galleries,
            'featured' => $featured
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/options/{id}/gallery', name: 'app_option_gallery_index')]
    public function home(Request $request, Advert $advert): Response
    {
        $options = $this->repository->getOptions(Product::CATEGORY_ADVERT_HOME_GALLERY_OPTION);

        $form = $this->createFormBuilder()
            ->add('option', HiddenType::class)
            ->setMethod('POST')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cartStorage->set([$form->getData()['option']]);
            $this->advertStorage->set($advert->getId());

            return $this->redirectToRoute('app_cart_validate');
        }

        return $this->render('site/option/home.html.twig', [
            'options' => $options,
            'advert' => $advert,
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/options/{id}/featured', name: 'app_option_featured_index')]
    public function featured(Request $request, Advert $advert): Response
    {
        $options = $this->repository->getOptions(Product::CATEGORY_ADVERT_FEATURED_OPTION);

        $form = $this->createFormBuilder()
            ->add('option', HiddenType::class)
            ->setMethod('POST')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cartStorage->set([$form->getData()['option']]);
            $this->advertStorage->set($advert->getId());

            return $this->redirectToRoute('app_cart_validate');
        }

        return $this->render('site/option/featured.html.twig', [
            'options' => $options,
            'advert' => $advert,
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/options/{id}/header', name: 'app_option_header_index')]
    public function header(Request $request, Advert $advert): Response
    {
        $options = $this->repository->getOptions(Product::CATEGORY_ADVERT_HEAD_OPTION);

        $form = $this->createFormBuilder()
            ->add('option', HiddenType::class)
            ->setMethod('POST')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cartStorage->set([$form->getData()['option']]);
            $this->advertStorage->set($advert->getId());

            return $this->redirectToRoute('app_cart_validate');
        }

        return $this->render('site/option/header.html.twig', [
            'options' => $options,
            'advert' => $advert,
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/options/{id}/urgent', name: 'app_option_urgent_index')]
    public function urgent(Request $request, Advert $advert): Response
    {
        $options = $this->repository->getOptions(Product::CATEGORY_ADVERT_URGENT_OPTION);

        $form = $this->createFormBuilder()
            ->add('option', HiddenType::class)
            ->setMethod('POST')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cartStorage->set([$form->getData()['option']]);
            $this->advertStorage->set($advert->getId());

            return $this->redirectToRoute('app_cart_validate');
        }

        return $this->render('site/option/urgent.html.twig', [
            'options' => $options,
            'advert' => $advert,
            'form' => $form->createView()
        ]);
    }
}
