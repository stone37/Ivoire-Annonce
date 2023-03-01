<?php

namespace App\Entity;

use App\Entity\Traits\DeletableTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\Notifiable;
use App\Entity\Traits\PremiumTrait;
use App\Entity\Traits\SocialLoggableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\UserRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['phone'], message: 'Il existe déjà un compte avec cet numéro de téléphone.')]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cet e-mail.', repositoryMethod: 'findByCaseInsensitive')]
#[UniqueEntity(fields: ['username'], repositoryMethod: 'findByCaseInsensitive')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use MediaTrait;
    use Notifiable;
    use SocialLoggableTrait;
    use DeletableTrait;
    use PremiumTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Entrez une adresse e-mail s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "L'adresse e-mail est trop courte.", maxMessage: "L'adresse e-mail est trop longue.", groups: ['Registration', 'Profile'])]
    #[Assert\Email(message: "L'adresse e-mail est invalide.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[SerializedName('password')]
    private $plainPassword;

    #[ORM\Column(length: 180, nullable: true)]
    private ?string $username = null;

    #[Assert\NotBlank(message: "Entrez un prénom s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "Le prénom est trop court", maxMessage: "Le prénom est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $firstname = null;

    #[Assert\NotBlank(message: "Entrez un nom s'il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 2, max: 180, minMessage: "Le nom est trop court", maxMessage: "Le nom est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $lastname = null;

    #[Assert\NotBlank(message: "Entrez un numéro de téléphone s''il vous plait.", groups: ['Registration', 'Profile'])]
    #[Assert\Length(min: 10, max: 20, minMessage: "Le numéro de téléphone est trop court.", maxMessage: "Le numéro de téléphone est trop long.", groups: ['Registration', 'Profile'])]
    #[ORM\Column(length: 25, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(nullable: true)]
    private ?bool $phoneState = false;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $birthDay = null;

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
    private ?string $linkedinAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $youtubeAddress = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $businessWebSite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $businessName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $businessCity = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $businessZone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $bannedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastLoginIp = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $lastLoginAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isVerified = null;

    #[ORM\Column(nullable: true)]
    private ?bool $subscribedToNewsletter = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmationToken;

    #[Assert\File(maxSize: '8M')]
    #[Vich\UploadableField(
        mapping: 'user',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file = null;

    #[ORM\Column(nullable: true)]
    private ?bool $drift = false;

    #[ORM\Column(nullable: true)]
    private ?bool $parrainageDrift = false;

    #[ORM\OneToOne]
    private ?Invitation $invitation = null;

    #[ORM\OneToOne(inversedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Wallet $wallet;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Advert::class, orphanRemoval: true)]
    private Collection $adverts;

    #[ORM\OneToMany(mappedBy: 'createdBy', targetEntity: Thread::class)]
    private Collection $threads;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Favorite::class, orphanRemoval: true)]
    private Collection $favorites;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Alert::class, orphanRemoval: true)]
    private Collection $alerts;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Commande::class, orphanRemoval: true)]
    private Collection $commandes;

    public function __construct()
    {
        $this->wallet = new Wallet();
        $this->adverts = new ArrayCollection();
        $this->threads = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->alerts = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = trim($username ?: '');

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function isPhoneState(): ?bool
    {
        return $this->phoneState;
    }

    public function setPhoneState(?bool $phoneState): self
    {
        $this->phoneState = $phoneState;

        return $this;
    }

    public function getBirthDay(): ?DateTimeInterface
    {
        return $this->birthDay;
    }

    public function setBirthDay(?DateTimeInterface $birthDay): self
    {
        $this->birthDay = $birthDay;

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

    public function getLinkedinAddress(): ?string
    {
        return $this->linkedinAddress;
    }

    public function setLinkedinAddress(?string $linkedinAddress): self
    {
        $this->linkedinAddress = $linkedinAddress;

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

    public function getBusinessWebSite(): ?string
    {
        return $this->businessWebSite;
    }

    public function setBusinessWebSite(?string $businessWebSite): self
    {
        $this->businessWebSite = $businessWebSite;

        return $this;
    }

    public function getBusinessName(): ?string
    {
        return $this->businessName;
    }

    public function setBusinessName(?string $businessName): self
    {
        $this->businessName = $businessName;

        return $this;
    }

    public function getBusinessCity(): ?string
    {
        return $this->businessCity;
    }

    public function setBusinessCity(?string $businessCity): self
    {
        $this->businessCity = $businessCity;

        return $this;
    }

    public function getBusinessZone(): ?string
    {
        return $this->businessZone;
    }

    public function setBusinessZone(?string $businessZone): self
    {
        $this->businessZone = $businessZone;

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

    public function getBannedAt(): ?DateTimeInterface
    {
        return $this->bannedAt;
    }

    public function setBannedAt(?DateTimeInterface $bannedAt): self
    {
        $this->bannedAt = $bannedAt;

        return $this;
    }

    public function isBanned(): bool
    {
        return null !== $this->bannedAt;
    }

    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    public function setLastLoginIp(?string $lastLoginIp): self
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    public function getLastLoginAt(): ?DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isSubscribedToNewsletter(): ?bool
    {
        return $this->subscribedToNewsletter;
    }

    public function setSubscribedToNewsletter(?bool $subscribedToNewsletter): self
    {
        $this->subscribedToNewsletter = $subscribedToNewsletter;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

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
            $this->updatedAt = new DateTimeImmutable();
        }

        return $this;
    }

    public function isDrift(): ?bool
    {
        return $this->drift;
    }

    public function setDrift(?bool $drift): self
    {
        $this->drift = $drift;

        return $this;
    }

    public function isParrainageDrift(): ?bool
    {
        return $this->parrainageDrift;
    }

    public function setParrainageDrift(?bool $parrainageDrift): self
    {
        $this->parrainageDrift = $parrainageDrift;

        return $this;
    }

    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }

    public function setInvitation(?Invitation $invitation): self
    {
        $this->invitation = $invitation;

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * @return Collection<int, Advert>
     */
    public function getAdverts(): Collection
    {
        return $this->adverts;
    }

    public function addAdvert(Advert $advert): self
    {
        if (!$this->adverts->contains($advert)) {
            $this->adverts[] = $advert;
            $advert->setOwner($this);
        }

        return $this;
    }

    public function removeAdvert(Advert $advert): self
    {
        if ($this->adverts->removeElement($advert)) {
            // set the owning side to null (unless already changed)
            if ($advert->getOwner() === $this) {
                $advert->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Thread>
     */
    public function getThreads(): Collection
    {
        return $this->threads;
    }

    public function addThread(Thread $thread): self
    {
        if (!$this->threads->contains($thread)) {
            $this->threads[] = $thread;
            $thread->setCreatedBy($this);
        }

        return $this;
    }

    public function removeThread(Thread $thread): self
    {
        if ($this->threads->removeElement($thread)) {
            // set the owning side to null (unless already changed)
            if ($thread->getCreatedBy() === $this) {
                $thread->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setOwner($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getOwner() === $this) {
                $favorite->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Alert>
     */
    public function getAlerts(): Collection
    {
        return $this->alerts;
    }

    public function addAlert(Alert $alert): self
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts[] = $alert;
            $alert->setOwner($this);
        }

        return $this;
    }

    public function removeAlert(Alert $alert): self
    {
        if ($this->alerts->removeElement($alert)) {
            // set the owning side to null (unless already changed)
            if ($alert->getOwner() === $this) {
                $alert->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setOwner($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getOwner() === $this) {
                $commande->setOwner(null);
            }
        }

        return $this;
    }

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->username,
            $this->email,
            $this->password
        ];
    }

    public function __unserialize(array $data): void
    {
        list (
            $this->id,
            $this->username,
            $this->email,
            $this->password
            ) = $data;
    }
}
