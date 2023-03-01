<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Exception\WrongWalletHistoryTypeException;
use App\Repository\WalletHistoryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WalletHistoryRepository::class)]
class WalletHistory
{
    const TYPE_INCOME = 1; // revenu
    const TYPE_SALARY = 2; // SALAIRE
    const TYPE_RECLAMATION = 3; // RÉCLAMATION

    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $amount = 0;

    #[ORM\Column(nullable: true)]
    private ?int $type = self::TYPE_INCOME;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $data = [];

    #[ORM\ManyToOne(inversedBy: 'histories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Wallet $wallet = null;

    /**
     * @throws WrongWalletHistoryTypeException
     */
    public function __construct($type = null)
    {
        $types = [self::TYPE_INCOME, self::TYPE_SALARY, self::TYPE_RECLAMATION];

        if (null !== $type) {
            if (false === array_key_exists($type, array_flip($types))) {
                throw new WrongWalletHistoryTypeException;
            }

            $this->setType($type);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(?array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;
        $wallet->addHistory($this);

        return $this;
    }

    public function __toString()
    {
        return (string) $this->name;
    }
}
