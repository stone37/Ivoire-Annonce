<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Exception\MoneyTypeNotExistException;
use App\Repository\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    const AVAILABLE_MONEY = 1;
    const FREEZE_MONEY = 2;

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $totalAmount = 0;

    #[ORM\Column(nullable: true)]
    private ?int $freezeAmount = 0;

    #[ORM\OneToMany(mappedBy: 'wallet', targetEntity: WalletHistory::class, orphanRemoval: true)]
    private Collection $histories;

    #[ORM\OneToOne(mappedBy: 'wallet')]
    private ?User $owner = null;

    #[Pure] public function __construct()
    {
        $this->histories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?int $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getFreezeAmount(): ?int
    {
        return $this->freezeAmount;
    }

    public function setFreezeAmount(?int $freezeAmount): self
    {
        $this->freezeAmount = $freezeAmount;

        return $this;
    }

    /**
     * @return Collection<int, WalletHistory>
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(WalletHistory $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setWallet($this);
        }

        return $this;
    }

    public function removeHistory(WalletHistory $history): self
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getWallet() === $this) {
                $history->setWallet(null);
            }
        }

        return $this;
    }

    public function getLastTransaction(): ?WalletHistory
    {
        return $this->getHistories()->last();
    }

    public function getAvailableAmount(): int
    {
        $amount = $this->getTotalAmount() - $this->getFreezeAmount();

        if ($amount > 0) {
            return $amount;
        }

        return 0;
    }

    /**
     * @throws MoneyTypeNotExistException
     */
    public function isAvailableMoney($amount, $type = self::AVAILABLE_MONEY): bool
    {
        switch ($type) {
            case self::AVAILABLE_MONEY:
                return $this->getAvailableAmount() >= $amount;
            case self::FREEZE_MONEY:
                return $this->getFreezeAmount() >= $amount && $this->getTotalAmount() >= $amount;
        }

        throw new MoneyTypeNotExistException;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        // unset the owning side of the relation if necessary
        if ($owner === null && $this->owner !== null) {
            $this->owner->setWallet(null);
        }

        // set the owning side of the relation if necessary
        if ($owner !== null && $owner->getWallet() !== $this) {
            $owner->setWallet($this);
        }

        $this->owner = $owner;

        return $this;
    }
}
