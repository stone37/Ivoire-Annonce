<?php

namespace App\Entity;

use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\AdvertRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdvertRepository::class)]
class Advert
{
    const TYPE_OFFER = 1;
    const TYPE_RESEARCH = 2;
    const TYPE_LOCATION = 3;
    const TYPE_EXCHANGE = 4;
    const TYPE_DON = 5;

    public const DELIVERY_PROCESSING = 1;
    public const SHIPMENT_PROCESSING = 2;

    public const TYPE_DATA = ['Offre', 'Recherche', 'Location', 'Troc', 'Don'];
    public const PROCESSING_DATA = ['Livraison possible', 'Expédition possible'];

    use PositionTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 180)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $title = null;

    #[Gedmo\Slug(fields: ['title'], unique: true)]
    #[ORM\Column(length: 180, nullable: true)]
    private ?string $slug = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    private ?bool $priceState = false;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $type = self::TYPE_OFFER;

    #[Assert\NotBlank]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $reference = null;

    #[Assert\NotBlank(groups: ['VOITURE', 'LOCATION_VOITURE', 'MOTO', 'BATEAUX'])]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $marque = null;

    #[Assert\NotBlank(groups: ['VOITURE', 'LOCATION_VOITURE'])]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $model = null;

    #[Assert\NotBlank(groups: ['VOITURE', 'LOCATION_VOITURE'])]
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $autoYear = null;

    #[Assert\NotBlank(groups: ['VOITURE', 'LOCATION_VOITURE'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autoType = null;

    #[Assert\NotBlank(groups: ['VOITURE', 'MOTO'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autoState = null;

    #[Assert\NotBlank(groups: ['VOITURE'])]
    #[ORM\Column(nullable: true)]
    private ?int $kilometrage = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $boiteVitesse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transmission = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeCarburant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autoColor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombrePorte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombrePlace = null;

    #[ORM\Column(nullable: true)]
    private ?int $cylindre = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $autreInformation = [];

    #[Assert\NotBlank(groups: ['APPARTEMENT', 'MAISON', 'CHAMBRE', 'COLOCATION', 'VILLA', 'STUDIO', 'TERRAIN', 'ESPACE_COMMERCIAUX'])]
    #[ORM\Column(nullable: true)]
    private ?int $surface = null;

    #[Assert\NotBlank(groups: ['APPARTEMENT', 'MAISON', 'VILLA'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nombrePiece = null;

    #[Assert\NotBlank(groups: ['APPARTEMENT', 'MAISON', 'VILLA'])]
    #[ORM\Column(nullable: true)]
    private ?int $nombreChambre = null;

    #[Assert\NotBlank(groups: ['APPARTEMENT', 'MAISON', 'VILLA'])]
    #[ORM\Column(nullable: true)]
    private ?int $nombreSalleBain = null;

    #[ORM\Column(nullable: true)]
    private ?int $surfaceBalcon = null;

    #[Assert\NotBlank(groups: ['APPARTEMENT', 'MAISON', 'CHAMBRE', 'COLOCATION', 'VILLA', 'STUDIO', 'ESPACE_COMMERCIAUX'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $immobilierState = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dateConstruction = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $standing = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cuisine = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombrePlacard = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $serviceInclus = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $access = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $exterior = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $interior = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $proximite = [];

    #[Assert\NotBlank(groups: ['ACHAT'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $state = null;

    #[Assert\NotBlank(groups: ['TABLETTE', 'TELEPHONE', 'ORDINATEUR_BUREAU', 'ORDINATEUR_PORTABLE', 'JV'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $brand = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $processing = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sex = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deniedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $deletedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $validatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $optionAdvertHeadEndAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $optionAdvertUrgentEndAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $optionAdvertHomeGalleryEndAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $optionAdvertFeaturedEndAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne]
    private ?Category $subCategory = null;

    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: AdvertPicture::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['principale' => 'desc'])]
    private Collection $images;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\ManyToOne(inversedBy: 'adverts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: AdvertRead::class, orphanRemoval: true)]
    private Collection $reads;

    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: Thread::class)]
    private Collection $threads;

    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: Favorite::class, orphanRemoval: true)]
    private Collection $favorites;

    #[ORM\OneToMany(mappedBy: 'advert', targetEntity: Commande::class)]
    private Collection $commandes;

    #[Pure] public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->reads = new ArrayCollection();
        $this->threads = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function isPriceState(): ?bool
    {
        return $this->priceState;
    }

    public function setPriceState(?bool $priceState): self
    {
        $this->priceState = $priceState;

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getAutoYear(): ?string
    {
        return $this->autoYear;
    }

    public function setAutoYear(?string $autoYear): self
    {
        $this->autoYear = $autoYear;

        return $this;
    }

    public function getAutoType(): ?string
    {
        return $this->autoType;
    }

    public function setAutoType(?string $autoType): self
    {
        $this->autoType = $autoType;

        return $this;
    }

    public function getAutoState(): ?string
    {
        return $this->autoState;
    }

    public function setAutoState(?string $autoState): self
    {
        $this->autoState = $autoState;

        return $this;
    }

    public function getKilometrage(): ?int
    {
        return $this->kilometrage;
    }

    public function setKilometrage(?int $kilometrage): self
    {
        $this->kilometrage = $kilometrage;

        return $this;
    }

    public function getBoiteVitesse(): ?string
    {
        return $this->boiteVitesse;
    }

    public function setBoiteVitesse(?string $boiteVitesse): self
    {
        $this->boiteVitesse = $boiteVitesse;

        return $this;
    }

    public function getTransmission(): ?string
    {
        return $this->transmission;
    }

    public function setTransmission(?string $transmission): self
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function getTypeCarburant(): ?string
    {
        return $this->typeCarburant;
    }

    public function setTypeCarburant(?string $typeCarburant): self
    {
        $this->typeCarburant = $typeCarburant;

        return $this;
    }

    public function getAutoColor(): ?string
    {
        return $this->autoColor;
    }

    public function setAutoColor(?string $autoColor): self
    {
        $this->autoColor = $autoColor;

        return $this;
    }

    public function getNombrePorte(): ?string
    {
        return $this->nombrePorte;
    }

    public function setNombrePorte(?string $nombrePorte): self
    {
        $this->nombrePorte = $nombrePorte;

        return $this;
    }

    public function getNombrePlace(): ?string
    {
        return $this->nombrePlace;
    }

    public function setNombrePlace(?string $nombrePlace): self
    {
        $this->nombrePlace = $nombrePlace;

        return $this;
    }

    public function getCylindre(): ?int
    {
        return $this->cylindre;
    }

    public function setCylindre(?int $cylindre): self
    {
        $this->cylindre = $cylindre;

        return $this;
    }

    public function getAutreInformation(): array
    {
        return $this->autreInformation;
    }

    public function setAutreInformation(?array $autreInformation): self
    {
        $this->autreInformation = $autreInformation;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(?int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getNombrePiece(): ?string
    {
        return $this->nombrePiece;
    }

    public function setNombrePiece(?string $nombrePiece): self
    {
        $this->nombrePiece = $nombrePiece;

        return $this;
    }

    public function getNombreChambre(): ?int
    {
        return $this->nombreChambre;
    }

    public function setNombreChambre(?int $nombreChambre): self
    {
        $this->nombreChambre = $nombreChambre;

        return $this;
    }

    public function getNombreSalleBain(): ?int
    {
        return $this->nombreSalleBain;
    }

    public function setNombreSalleBain(?int $nombreSalleBain): self
    {
        $this->nombreSalleBain = $nombreSalleBain;

        return $this;
    }

    public function getSurfaceBalcon(): ?int
    {
        return $this->surfaceBalcon;
    }

    public function setSurfaceBalcon(?int $surfaceBalcon): self
    {
        $this->surfaceBalcon = $surfaceBalcon;

        return $this;
    }

    public function getImmobilierState(): ?string
    {
        return $this->immobilierState;
    }

    public function setImmobilierState(?string $immobilierState): self
    {
        $this->immobilierState = $immobilierState;

        return $this;
    }

    public function getDateConstruction(): ?string
    {
        return $this->dateConstruction;
    }

    public function setDateConstruction(?string $dateConstruction): self
    {
        $this->dateConstruction = $dateConstruction;

        return $this;
    }

    public function getStanding(): ?string
    {
        return $this->standing;
    }

    public function setStanding(?string $standing): self
    {
        $this->standing = $standing;

        return $this;
    }

    public function getCuisine(): ?string
    {
        return $this->cuisine;
    }

    public function setCuisine(?string $cuisine): self
    {
        $this->cuisine = $cuisine;

        return $this;
    }

    public function getNombrePlacard(): ?int
    {
        return $this->nombrePlacard;
    }

    public function setNombrePlacard(?int $nombrePlacard): self
    {
        $this->nombrePlacard = $nombrePlacard;

        return $this;
    }

    public function getServiceInclus(): array
    {
        return $this->serviceInclus;
    }

    public function setServiceInclus(?array $serviceInclus): self
    {
        $this->serviceInclus = $serviceInclus;

        return $this;
    }

    public function getAccess(): array
    {
        return $this->access;
    }

    public function setAccess(?array $access): self
    {
        $this->access = $access;

        return $this;
    }

    public function getExterior(): array
    {
        return $this->exterior;
    }

    public function setExterior(?array $exterior): self
    {
        $this->exterior = $exterior;

        return $this;
    }

    public function getInterior(): array
    {
        return $this->interior;
    }

    public function setInterior(?array $interior): self
    {
        $this->interior = $interior;

        return $this;
    }

    public function getProximite(): array
    {
        return $this->proximite;
    }

    public function setProximite(?array $proximite): self
    {
        $this->proximite = $proximite;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getProcessing(): ?array
    {
        return $this->processing;
    }

    public function setProcessing(?array $processing): self
    {
        $this->processing = $processing;

        return $this;
    }

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(?string $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function getDeniedAt(): ?DateTimeInterface
    {
        return $this->deniedAt;
    }

    public function setDeniedAt(?DateTimeInterface $deniedAt): self
    {
        $this->deniedAt = $deniedAt;

        return $this;
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getValidatedAt(): ?DateTimeInterface
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?DateTimeInterface $validatedAt): self
    {
        $this->validatedAt = $validatedAt;

        return $this;
    }

    public function getOptionAdvertHeadEndAt(): ?DateTimeImmutable
    {
        return $this->optionAdvertHeadEndAt;
    }

    public function setOptionAdvertHeadEndAt(?DateTimeImmutable $optionAdvertHeadEndAt): self
    {
        $this->optionAdvertHeadEndAt = $optionAdvertHeadEndAt;

        return $this;
    }

    public function getOptionAdvertUrgentEndAt(): ?DateTimeImmutable
    {
        return $this->optionAdvertUrgentEndAt;
    }

    public function setOptionAdvertUrgentEndAt(?DateTimeImmutable $optionAdvertUrgentEndAt): self
    {
        $this->optionAdvertUrgentEndAt = $optionAdvertUrgentEndAt;

        return $this;
    }

    public function getOptionAdvertHomeGalleryEndAt(): ?DateTimeImmutable
    {
        return $this->optionAdvertHomeGalleryEndAt;
    }

    public function setOptionAdvertHomeGalleryEndAt(?DateTimeImmutable $optionAdvertHomeGalleryEndAt): self
    {
        $this->optionAdvertHomeGalleryEndAt = $optionAdvertHomeGalleryEndAt;

        return $this;
    }

    public function getOptionAdvertFeaturedEndAt(): ?DateTimeImmutable
    {
        return $this->optionAdvertFeaturedEndAt;
    }

    public function setOptionAdvertFeaturedEndAt(?DateTimeImmutable $optionAdvertFeaturedEndAt): self
    {
        $this->optionAdvertFeaturedEndAt = $optionAdvertFeaturedEndAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getSubCategory(): ?Category
    {
        return $this->subCategory;
    }

    public function setSubCategory(?Category $subCategory): self
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    /**
     * @return Collection<int, AdvertPicture>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(AdvertPicture $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAdvert($this);
        }

        return $this;
    }

    public function removeImage(AdvertPicture $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAdvert() === $this) {
                $image->setAdvert(null);
            }
        }

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, AdvertRead>
     */
    public function getReads(): Collection
    {
        return $this->reads;
    }

    public function addRead(AdvertRead $read): self
    {
        if (!$this->reads->contains($read)) {
            $this->reads[] = $read;
            $read->setAdvert($this);
        }

        return $this;
    }

    public function removeRead(AdvertRead $read): self
    {
        if ($this->reads->removeElement($read)) {
            // set the owning side to null (unless already changed)
            if ($read->getAdvert() === $this) {
                $read->setAdvert(null);
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
            $thread->setAdvert($this);
        }

        return $this;
    }

    public function removeThread(Thread $thread): self
    {
        if ($this->threads->removeElement($thread)) {
            // set the owning side to null (unless already changed)
            if ($thread->getAdvert() === $this) {
                $thread->setAdvert(null);
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
            $favorite->setAdvert($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getAdvert() === $this) {
                $favorite->setAdvert(null);
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
            $commande->setAdvert($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getAdvert() === $this) {
                $commande->setAdvert(null);
            }
        }

        return $this;
    }
}
