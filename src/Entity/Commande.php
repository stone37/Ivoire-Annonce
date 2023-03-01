<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    public const CARD_PAYMENT = 'card';
    public const CREDIT_PAYMENT = 'credit';

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $validated = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    private ?int $amount = 0;

    #[ORM\Column(nullable: true)]
    private ?int $amountTotal = 0;

    #[ORM\Column(nullable: true)]
    private ?int $taxeAmount = null;

    #[ORM\Column(nullable: true)]
    private ?int $discountAmount = null;

    #[ORM\Column(nullable: true)]
    private ?string $paymentMethod = self::CARD_PAYMENT;

    #[ORM\OneToOne(mappedBy: 'commande', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Discount $discount = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?Advert $advert = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: Item::class, cascade: ['remove'])]
    private Collection $items;

    #[Pure] public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(?bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmountTotal(): ?int
    {
        return $this->amountTotal;
    }

    public function setAmountTotal(?int $amountTotal): self
    {
        $this->amountTotal = $amountTotal;

        return $this;
    }

    public function getTaxeAmount(): ?int
    {
        return $this->taxeAmount;
    }

    public function setTaxeAmount(?int $taxeAmount): self
    {
        $this->taxeAmount = $taxeAmount;

        return $this;
    }

    public function getDiscountAmount(): ?int
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(?int $discountAmount): self
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(?Discount $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User|UserInterface $owner
     * @return $this
     */
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
            $item->setCommande($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getCommande() === $this) {
                $item->setCommande(null);
            }
        }

        return $this;
    }
}
