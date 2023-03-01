<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniqueCurrencyPair;
use App\Validator\DifferentSourceTargetCurrency;

#[UniqueCurrencyPair]
#[DifferentSourceTargetCurrency]
#[ORM\Entity(repositoryClass: ExchangeRateRepository::class)]
class ExchangeRate
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez entrer le rapport de taux de change.')]
    #[Assert\GreaterThan(value: 0, message: 'Le rapport doit être supérieur à 0.')]
    #[ORM\Column(nullable: true)]
    private ?float $ratio = null;

    #[Assert\NotBlank(message: 'Veuillez indiquer depuis quelle devise le taux de change doit être calculé.')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Currency $sourceCurrency = null;

    #[Assert\NotBlank(message: 'Veuillez indiquer vers quelle devise le taux de change doit être calculé.')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Currency $targetCurrency = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRatio(): ?float
    {
        return $this->ratio;
    }

    public function setRatio(?float $ratio): self
    {
        $this->ratio = $ratio;

        return $this;
    }

    public function getSourceCurrency(): ?Currency
    {
        return $this->sourceCurrency;
    }

    public function setSourceCurrency(?Currency $sourceCurrency): self
    {
        $this->sourceCurrency = $sourceCurrency;

        return $this;
    }

    public function getTargetCurrency(): ?Currency
    {
        return $this->targetCurrency;
    }

    public function setTargetCurrency(?Currency $targetCurrency): self
    {
        $this->targetCurrency = $targetCurrency;

        return $this;
    }
}
