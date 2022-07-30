<?php

namespace App\Entity;

use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\SettingsRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    use MediaTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fax = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebookAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitterAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instagramAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $youtubeAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedinAddress = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeThread = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeAdFavorite = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeAlert = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeAdvertSimilar = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeCredit = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeCardPayment = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeVignette = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activePub = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeParrainage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberAdvertPerPage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberUserAdvertPerPage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numberUserAdvertFavoritePerPage = null;

    #[ORM\Column(nullable: true)]
    private ?int $parrainCreditOffer = null;

    #[ORM\Column]
    private ?int $fioleCreditOffer = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activeRegisterDrift = null;

    #[ORM\Column(nullable: true)]
    private ?int $registerDriftCreditOffer = null;

    #[ORM\Column(nullable: true)]
    private ?int $parrainageNumberAdvertRequired = null;

    #[ORM\Column(nullable: true)]
    private ?int $registerDriftNumberAdvertRequired = null;

    #[Vich\UploadableField(
        mapping: 'settings',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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

    public function getFax(): ?string
    {
        return $this->fax;
    }

    public function setFax(?string $fax): self
    {
        $this->fax = $fax;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getFacebookAddress(): ?string
    {
        return $this->facebookAddress;
    }

    public function setFacebookAddress(?string $facebookAddress): self
    {
        $this->facebookAddress = $facebookAddress;

        return $this;
    }

    public function getTwitterAddress(): ?string
    {
        return $this->twitterAddress;
    }

    public function setTwitterAddress(?string $twitterAddress): self
    {
        $this->twitterAddress = $twitterAddress;

        return $this;
    }

    public function getInstagramAddress(): ?string
    {
        return $this->instagramAddress;
    }

    public function setInstagramAddress(?string $instagramAddress): self
    {
        $this->instagramAddress = $instagramAddress;

        return $this;
    }

    public function getYoutubeAddress(): ?string
    {
        return $this->youtubeAddress;
    }

    public function setYoutubeAddress(?string $youtubeAddress): self
    {
        $this->youtubeAddress = $youtubeAddress;

        return $this;
    }

    public function getLinkedinAddress(): ?string
    {
        return $this->linkedinAddress;
    }

    public function setLinkedinAddress(?string $linkedinAddress): self
    {
        $this->linkedinAddress = $linkedinAddress;

        return $this;
    }

    public function isActiveThread(): ?bool
    {
        return $this->activeThread;
    }

    public function setActiveThread(?bool $activeThread): self
    {
        $this->activeThread = $activeThread;

        return $this;
    }

    public function isActiveAdFavorite(): ?bool
    {
        return $this->activeAdFavorite;
    }

    public function setActiveAdFavorite(?bool $activeAdFavorite): self
    {
        $this->activeAdFavorite = $activeAdFavorite;

        return $this;
    }

    public function isActiveAlert(): ?bool
    {
        return $this->activeAlert;
    }

    public function setActiveAlert(?bool $activeAlert): self
    {
        $this->activeAlert = $activeAlert;

        return $this;
    }

    public function isActiveAdvertSimilar(): ?bool
    {
        return $this->activeAdvertSimilar;
    }

    public function setActiveAdvertSimilar(?bool $activeAdvertSimilar): self
    {
        $this->activeAdvertSimilar = $activeAdvertSimilar;

        return $this;
    }

    public function isActiveCredit(): ?bool
    {
        return $this->activeCredit;
    }

    public function setActiveCredit(?bool $activeCredit): self
    {
        $this->activeCredit = $activeCredit;

        return $this;
    }

    public function isActiveCardPayment(): ?bool
    {
        return $this->activeCardPayment;
    }

    public function setActiveCardPayment(?bool $activeCardPayment): self
    {
        $this->activeCardPayment = $activeCardPayment;

        return $this;
    }

    public function isActiveVignette(): ?bool
    {
        return $this->activeVignette;
    }

    public function setActiveVignette(?bool $activeVignette): self
    {
        $this->activeVignette = $activeVignette;

        return $this;
    }

    public function isActivePub(): ?bool
    {
        return $this->activePub;
    }

    public function setActivePub(?bool $activePub): self
    {
        $this->activePub = $activePub;

        return $this;
    }

    public function isActiveParrainage(): ?bool
    {
        return $this->activeParrainage;
    }

    public function setActiveParrainage(?bool $activeParrainage): self
    {
        $this->activeParrainage = $activeParrainage;

        return $this;
    }

    public function getNumberAdvertPerPage(): ?int
    {
        return $this->numberAdvertPerPage;
    }

    public function setNumberAdvertPerPage(?int $numberAdvertPerPage): self
    {
        $this->numberAdvertPerPage = $numberAdvertPerPage;

        return $this;
    }

    public function getNumberUserAdvertPerPage(): ?int
    {
        return $this->numberUserAdvertPerPage;
    }

    public function setNumberUserAdvertPerPage(?int $numberUserAdvertPerPage): self
    {
        $this->numberUserAdvertPerPage = $numberUserAdvertPerPage;

        return $this;
    }

    public function getNumberUserAdvertFavoritePerPage(): ?int
    {
        return $this->numberUserAdvertFavoritePerPage;
    }

    public function setNumberUserAdvertFavoritePerPage(?int $numberUserAdvertFavoritePerPage): self
    {
        $this->numberUserAdvertFavoritePerPage = $numberUserAdvertFavoritePerPage;

        return $this;
    }

    public function getParrainCreditOffer(): ?int
    {
        return $this->parrainCreditOffer;
    }

    public function setParrainCreditOffer(?int $parrainCreditOffer): self
    {
        $this->parrainCreditOffer = $parrainCreditOffer;

        return $this;
    }

    public function getFioleCreditOffer(): ?int
    {
        return $this->fioleCreditOffer;
    }

    public function setFioleCreditOffer(int $fioleCreditOffer): self
    {
        $this->fioleCreditOffer = $fioleCreditOffer;

        return $this;
    }

    public function isActiveRegisterDrift(): ?bool
    {
        return $this->activeRegisterDrift;
    }

    public function setActiveRegisterDrift(?bool $activeRegisterDrift): self
    {
        $this->activeRegisterDrift = $activeRegisterDrift;

        return $this;
    }

    public function getRegisterDriftCreditOffer(): ?int
    {
        return $this->registerDriftCreditOffer;
    }

    public function setRegisterDriftCreditOffer(?int $registerDriftCreditOffer): self
    {
        $this->registerDriftCreditOffer = $registerDriftCreditOffer;

        return $this;
    }

    public function getParrainageNumberAdvertRequired(): ?int
    {
        return $this->parrainageNumberAdvertRequired;
    }

    public function setParrainageNumberAdvertRequired(?int $parrainageNumberAdvertRequired): self
    {
        $this->parrainageNumbreAdvertRequiere = $parrainageNumberAdvertRequired;

        return $this;
    }

    public function getRegisterDriftNumberAdvertRequired(): ?int
    {
        return $this->registerDriftNumberAdvertRequired;
    }

    public function setRegisterDriftNumberAdvertRequired(?int $registerDriftNumberAdvertRequired): self
    {
        $this->registerDriftNumberAdvertRequired = $registerDriftNumberAdvertRequired;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        if (null !== $file) {
            $this->updatedAt = new DateTime();
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
