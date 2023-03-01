<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\Discount;
use App\Entity\Item;
use App\Entity\Product;
use JetBrains\PhpStorm\Pure;

class Summary
{
    private Commande $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    public function getAmountTotal(): int
    {
        return (int) $this->commande->getAmountTotal();
    }

    #[Pure] public function getAmountBeforeDiscount(): int
    {
        return (int) $this->commande->getAmount();
    }

    #[Pure] public function getTaxeAmount(): int
    {
        return (int) $this->commande->getTaxeAmount();
    }

    public function amountPaid(): int
    {
        return ($this->getAmountTotal() - $this->getDiscount());
    }

    public function amountCommission(): int
    {
        return $this->getAmountTotal() - $this->getTaxeAmount();
    }

    public function getDiscount(): int
    {
        $price = 0;
        $discount = $this->commande->getDiscount();

        if ($discount) {
            if ($discount->getType() === Discount::FIXED_DISCOUNT) {
                $price = ($this->getAmountBeforeDiscount() - $discount->getDiscount());
            } elseif ($discount->getType() === Discount::PERCENTAGE_DISCOUNT) {
                $price = (($this->getAmountBeforeDiscount() * $discount->getDiscount()) / 100);
            }
        } else {
            $price = $this->commande->getDiscountAmount();
        }

        return (int) $price;
    }

    public function hasDiscount(): bool
    {
        return (bool) $this->commande->getDiscount();
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }

    public function hasCredit(): bool
    {
        $result = false;

        /** @var Item $item */
        foreach ($this->commande->getItems() as $item) {
            if ($item->getProduct()->getCategory() === Product::CATEGORY_CREDIT) {
                $result = true;
            }
        }

        return $result;
    }
}
