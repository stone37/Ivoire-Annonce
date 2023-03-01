<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\DiscountType;
use App\Form\PaymentType;
use App\Repository\CommandeRepository;
use App\Service\Summary;
use App\Storage\CartStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private CartStorage $storage
    )
    {
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/cart/validate', name: 'app_cart_validate')]
    public function index(Request $request): RedirectResponse|Response
    {
        $prepareCommande = $this->forward('App\Controller\CommandeController::prepareCommande');

        $commande = $this->commandeRepository->find($prepareCommande->getContent());
        $summary = new Summary($commande);

        $paymentForm = $this->createForm(PaymentType::class, $commande);
        $discountForm = $this->createForm(DiscountType::class, $commande);

        $paymentForm->handleRequest($request);

        if ($paymentForm->isSubmitted() && $paymentForm->isValid()) {

            if ($commande->getPaymentMethod() === Commande::CARD_PAYMENT) {
                return $this->redirectToRoute('app_commande_validate');
            } else {
                return $this->redirectToRoute('app_commande_credit_validate');
            }
        }

        return $this->render('site/cart/validate.html.twig', [
            'payment_form' => $paymentForm->createView(),
            'discount_form' => $discountForm->createView(),
            'commande' => $summary
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/cart/{id}/add', name: 'app_cart_add', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function add(Request $request, int $id): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Bad request');
        }

        if (!$this->storage->has()) {
            $this->storage->set([]);
        }

        $cart = $this->storage->get();

        if (in_array($id, $cart)) {
            return new JsonResponse(false);
        }

        $cart[] = $id;
        $this->storage->set($cart);

        return new JsonResponse(true);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/cart/{id}/replace/{newId}', name: 'app_cart_replace', requirements: ['id' => '\d+', 'newId' => '\d+'], options: ['expose' => true])]
    public function replace(Request $request, int $id, int $newId): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Bad request');
        }

        $cart = $this->storage->get();

        if (in_array($id, $cart)) {
            unset($cart[array_search($id, $cart)]);
            $this->storage->set($cart);
        }

        $cart[] = $newId;
        $this->storage->set($cart);

        return new JsonResponse(true);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/cart/{id}/delete', name: 'app_cart_delete', requirements: ['id' => '\d+'], options: ['expose' => true])]
    public function delete(Request $request, int $id): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            $this->createNotFoundException('Bad request');
        }

        $cart = $this->storage->get();

        if (!in_array($id, $cart)) {
            return new JsonResponse(false);
        }

        unset($cart[array_search($id, $cart)]);
        $this->storage->set($cart);

        return new JsonResponse(true);
    }
}
