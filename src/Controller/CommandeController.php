<?php

namespace App\Controller;

use App\Collector\AppCollector;
use App\Controller\Traits\ControllerTrait;
use App\Entity\Item;
use App\Entity\Product;
use App\Entity\Settings;
use App\Event\PaymentEvent;
use App\Exception\MoneyTypeNotExistException;
use App\Exception\NotEnoughMoneyException;
use App\Manager\OrderManager;
use App\Manager\SettingsManager;
use App\Repository\CommandeRepository;
use App\Repository\ProductRepository;
use App\Service\Summary;
use App\Service\TransactionService;
use App\Storage\AdvertStorage;
use App\Storage\CartStorage;
use App\Storage\CommandeStorage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;

class CommandeController extends AbstractController
{
    use ControllerTrait;

    private ?Settings $settings;

    public function __construct(
        private OrderManager $manager,
        private ProductRepository $productRepository,
        private CartStorage $cartStorage,
        private AdvertStorage $advertStorage,
        private CommandeStorage $storage,
        private CommandeRepository $repository,
        private EventDispatcherInterface $dispatcher,
        private Breadcrumbs $breadcrumbs,
        private SettingsManager $settingsManager,
        private AppCollector $collector,
        private TransactionService $transactionService
    )
    {
        $this->settings = $settingsManager->get();
    }

    public function prepareCommande(): Response
    {
        $this->manager->clearItems();

        $amountTotal = 0;
        $products = $this->productRepository->findByArray(array_values($this->cartStorage->get()));

        $commande = ($this->manager->getCurrent())
            ->setValidated(false)
            ->setReference(null)
            ->setTaxeAmount(0)
            ->setDiscountAmount(0);

        foreach ($products as $product) {
            $this->manager->addItem($commande, $product);
            $amountTotal += $product->getPrice();
        }

        $commande
            ->setAmount($amountTotal)
            ->setAmountTotal($amountTotal);

        if (!$this->storage->has()) {
            $this->repository->add($commande, false);
        }

        $this->repository->flush();

        $this->storage->set($commande->getId());

        return new Response($commande->getId());
    }


    #[Route(path: '/commande/validate', name: 'app_commande_validate')]
    public function validate(): RedirectResponse
    {
        $commande = $this->manager->getCurrent();

        if (!$commande || $commande->isValidated()) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        return $this->redirectToRoute('app_commande_pay');
    }

    /**
     * @throws MoneyTypeNotExistException
     * @throws NotEnoughMoneyException
     */
    #[Route(path: '/commande/validate', name: 'app_commande_credit_validate')]
    public function creditValidate(): RedirectResponse
    {
        $commande = $this->manager->getCurrent();

        if (!$commande || $commande->isValidated()) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        $summary = $this->manager->summary();
        $user = $this->getUserOrThrow();

        if ($user->getWallet()->isAvailableMoney($summary->amountPaid())) {
            /** @var Item $item */
            $item = $commande->getItems()->first();

            $this->transactionService->subMoney($user, $summary->amountPaid(), $this->getName($item->getProduct()));

            $this->dispatcher->dispatch(new PaymentEvent($commande));

            $this->storage->remove();
            $this->cartStorage->init();
            $this->advertStorage->remove();

            return $this->redirectToRoute('app_commande_success');
        } else {
            $this->addFlash('error', 'Désolé, votre paiement a échoué cause: credit est insuffisant');

            return $this->redirectToRoute('app_cart_validate');
        }
    }

    #[Route(path: '/commande/payment', name: 'app_commande_pay')]
    public function payment(): RedirectResponse
    {
        $commande = $this->manager->getCurrent();

        if (!$commande || $commande->isValidated()) {
            throw $this->createNotFoundException('La commande n\'existe pas...');
        }

        $this->dispatcher->dispatch(new PaymentEvent($commande));

        $this->storage->remove();
        $this->cartStorage->init();
        $this->advertStorage->remove();

        return $this->redirectToRoute('app_commande_success');
    }

    #[Route(path: '/commande/validate/success', name: 'app_commande_success')]
    public function success(): Response
    {
        $this->breadcrumb($this->breadcrumbs)->addItem('Felicitation, votre paiement a été effectué avec succès');

        $commande =  $this->getUserOrThrow()->getCommandes()->last();

        return $this->render('site/commande/success.html.twig', [
            'commande' => new Summary($commande)
        ]);
    }

    public function getName(Product $product): string
    {
        if ($product->getCategory() === Product::CATEGORY_ADVERT_OPTION) {
            return 'Paiement d\'option pour une annonce';
        } else {
            return 'Paiement de pack d\'abonnement';
        }
    }
}

