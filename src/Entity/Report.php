<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Assert\Expression(expression: 'this.getAdvert() !== null', message: 'Un signalement doit être associé à une annonce')]
#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez renseigne votre email')]
    #[Assert\Email(message: "Votre email n'est pas valide")]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $email = null;

    #[Assert\NotBlank(message: 'Veuillez renseigne la raison', normalizer: 'trim')]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reason = null;

    #[Assert\NotBlank(message: "Merci de nous préciser les détails de l'annonce vous amenant à la signaler")]
    #[Assert\Length(min: 10, minMessage: "Votre message doit contenir au moin {{ limit }} caractères")]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Advert $advert = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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
}
