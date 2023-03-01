<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\MediaTrait;
use App\Entity\Traits\PositionTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Gedmo\Sluggable\Handler\TreeSlugHandler;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

#[Vich\Uploadable]
#[Gedmo\Tree(type: 'nested')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    use PositionTrait;
    use EnabledTrait;
    use TimestampableTrait;
    use MediaTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Gedmo\TreeParent]
    #[Gedmo\SortableGroup]
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'asc'])]
    private Collection $children;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[Gedmo\SlugHandler(class: TreeSlugHandler::class, options: ['parentRelationField' => 'parent'])]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $permalink = null;

    #[Vich\UploadableField(
        mapping: 'category',
        fileNameProperty: 'fileName',
        size: 'fileSize',
        mimeType: 'fileMimeType',
        originalName: 'fileOriginalName'
    )]
    private ?File $file = null;

    #[Gedmo\TreeRoot]
    #[ORM\Column(nullable: true)]
    private ?int $rootNode = null;

    #[Gedmo\TreeLeft]
    #[ORM\Column(nullable: true)]
    private ?int $leftNode = null;

    #[Gedmo\TreeRight]
    #[ORM\Column(nullable: true)]
    private ?int $rightNode = null;

    #[Gedmo\TreeLevel]
    #[ORM\Column(nullable: true)]
    private ?int $levelDepth = null;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    private ?CategoryPremium $premiums = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPermalink(): ?string
    {
        return $this->permalink;
    }

    public function setPermalink(string $permalink): self
    {
        $this->permalink = $permalink;

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

    public function getRootNode(): ?int
    {
        return $this->rootNode;
    }

    public function setRootNode(?int $rootNode): self
    {
        $this->rootNode = $rootNode;

        return $this;
    }

    public function getLeftNode(): ?int
    {
        return $this->leftNode;
    }

    public function setLeftNode(?int $leftNode): self
    {
        $this->leftNode = $leftNode;

        return $this;
    }

    public function getRightNode(): ?int
    {
        return $this->rightNode;
    }

    public function setRightNode(?int $rightNode): self
    {
        $this->rightNode = $rightNode;

        return $this;
    }

    public function getLevelDepth(): ?int
    {
        return $this->levelDepth;
    }

    public function setLevelDepth(?int $levelDepth): self
    {
        $this->levelDepth = $levelDepth;

        return $this;
    }

    public function getPremiums(): ?CategoryPremium
    {
        return $this->premiums;
    }

    public function setPremiums(?CategoryPremium $premiums): self
    {
        $this->premiums = $premiums;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }
}
