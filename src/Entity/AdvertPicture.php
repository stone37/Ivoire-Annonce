<?php

namespace App\Entity;

use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\AdvertPictureRepository;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: AdvertPictureRepository::class)]
class AdvertPicture
{
    use PositionTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $extension = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[Assert\File(
        maxSize: '8000k',
        mimeTypes: ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'],
        maxSizeMessage: 'Le fichier excède 8000Ko',
        mimeTypesMessage: 'Format non autorisés. Formats autorisés: png, jpeg, jpg, gif'
    )]
    private ?File $file = null;

    private ?string $tempFilename = null;

    #[ORM\Column(nullable: true)]
    private ?bool $principale = false;

    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Advert $advert = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;

        if (null !== $this->extension) {
            $this->tempFilename = $this->extension;

            $this->extension = null;
            $this->name = null;
        }

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function preUpload(): void
    {
        if (null === $this->file) {return;}

        $this->extension = $this->file->guessExtension();
        $this->name = $this->file->getFilename();
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function upload(): void
    {
        if (null === $this->file) {return;}

        if (null !== $this->tempFilename) {
            $oldFile = $this->getUploadRootDir() . '/' . $this->id . '.' . $this->extension;
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        $this->file->move(
            $this->getUploadRootDir(),
            $this->id . '.' . $this->extension
        );
    }

    #[ORM\PreRemove]
    public function preRemoveUpload(): self
    {
        $this->tempFilename = $this->getUploadRootDir() . '/' . $this->id . '.' . $this->extension;

        return $this;
    }

    #[ORM\PostRemove]
    public function removeUpload(): self
    {
        if (file_exists($this->tempFilename)) {
            unlink($this->tempFilename);
        }

        return $this;
    }

    /**
     * Retourne le chemin relatif vers l'image pour un navigateur
     */
    public function getUploadDir(): string
    {
        return 'uploads/images/adverts';
    }

    /**
     * Retourne le chemin relatif vers l'image pour le code PHP
     */
    #[Pure] protected function getUploadRootDir(): string
    {
        return __DIR__ . '/../../public/' . $this->getUploadDir();
    }

    #[Pure] public function getWebPath()
    {
        return $this->getUploadDir() . '/' . $this->getId() . '.' . $this->getExtension();
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isPrincipale(): ?bool
    {
        return $this->principale;
    }

    public function setPrincipale(?bool $principale): self
    {
        $this->principale = $principale;

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
