<?php

namespace App\Manager;

use App\Entity\Commande;
use App\Entity\Discount;
use App\Entity\Item;
use App\Entity\Product;
use App\Event\OrderEvent;
use App\Repository\CommandeRepository;
use App\Repository\ItemRepository;
use App\Service\Summary;
use App\Storage\CommandeStorage;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;

class OrderManager
{
    private Commande $commande;

    public function __construct(
        private Security $security,
        private EventDispatcherInterface $dispatcher,
        private CommandeRepository $repository,
        private ItemRepository $itemRepository,
        private CommandeStorage $storage
    )
    {
        $this->commande = $this->getCurrent();
    }

    public function getCurrent(): Commande
    {
        $commande = $this->storage->getCommande();

        if ($commande !== null) {
            return $commande;
        }

        $commande = new Commande();

        if ($this->security->getUser()) {
            $commande->setOwner($this->security->getUser());
        }

        return $commande;
    }

    public function setDiscount(Discount $discount): void
    {
        if ($this->commande) {
            $this->commande->setDiscount($discount);

            $this->dispatcher->dispatch(new OrderEvent($this->commande));
            $this->repository->add($this->commande, true);
        }
    }

    public function clearItems(): void
    {
        foreach ($this->commande->getItems() as $item) {
            $this->itemRepository->remove($item, false);
        }

        $this->itemRepository->flush();
    }

    public function addItem(Commande $commande, Product $product): void
    {
        $item = (new Item())
            ->setProduct($product)
            ->setQuantity(1)
            ->setAmount($product->getPrice());

        $this->itemRepository->add($item, false);

        $commande->addItem($item);

        $this->dispatcher->dispatch(new OrderEvent($this->commande));
    }

    #[Pure] public function summary(): Summary
    {
        return new Summary($this->commande);
    }
}
