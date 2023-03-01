<?php

namespace App\Controller;

use App\Form\DiscountType;
use App\Manager\OrderManager;
use App\Repository\DiscountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DiscountController extends AbstractController
{
    public function __construct(
        private OrderManager $manager,
        private DiscountRepository $repository
    )
    {
    }

    #[Route(path: '/discount', name: 'app_discount_index')]
    public function index(Request $request): RedirectResponse
    {
        $form = $this->createForm(DiscountType::class, $this->manager->getCurrent());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discount = $this->repository->findOneBy(['code' => $form->get('discountCode')->getData()]);

            if ($discount !== null) {
                if ($discount->getUtilisation() === $discount->getUtiliser()) {
                    $this->addFlash('error', 'Ce code promo n\'est plus valide');

                    return $this->redirectToRoute('app_cart_validate');
                }

                $this->manager->setDiscount($discount);

                $this->addFlash('success', 'Le code promo a été utilisé');
            } else {
                $this->addFlash('error', 'Ce code promo n\'existe pas');
            }
        }

        return $this->redirectToRoute('app_cart_validate');
    }
}
