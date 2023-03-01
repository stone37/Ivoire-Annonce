<?php

namespace App\Controller\Account;

use App\Entity\Settings;
use App\Manager\SettingsManager;
use App\Repository\ProductRepository;
use App\Storage\CartStorage;
use App\Storage\CommandeStorage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/u')]
class CreditController extends AbstractController
{
    private ?Settings $settings;

    public function __construct(
        SettingsManager $manager,
        private CommandeStorage $storage,
        private CartStorage $cartStorage,
        private ProductRepository $productRepository
    )
    {
        $this->settings = $manager->get();
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/credits', name: 'app_user_credit_index', methods: ['GET', 'POST'])]
    public function index(Request $request): NotFoundHttpException|RedirectResponse|Response
    {
        if (!$this->settings->isActiveCredit()) {
            return $this->createNotFoundException('Bad request');
        }

        $this->storage->remove();
        $this->cartStorage->init();

        $products = $this->productRepository->getEnabledCredits();

        $form = $this->createFormBuilder()
                ->add('credit', HiddenType::class)
                ->setMethod('POST')
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->cartStorage->set([$form->getData()['credit']]);

            return $this->redirectToRoute('app_cart_validate');
        }

        return $this->render('user/credit/index.html.twig', [
            'form' => $form->createView(),
            'products' => $products
        ]);
    }
}