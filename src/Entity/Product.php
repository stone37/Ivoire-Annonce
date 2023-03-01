<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    public const CATEGORY_CREDIT = 'credit';
    public const CATEGORY_ADVERT_OPTION = 'advert-option';
    public const CATEGORY_PREMIUM_PACK = 'premium-pack';
    public const CATEGORY_THUMBNAIL = 'thumbnail';

    public const CATEGORY_ADVERT_HEAD_OPTION = 1;
    public const CATEGORY_ADVERT_URGENT_OPTION = 2;
    public const CATEGORY_ADVERT_HOME_GALLERY_OPTION = 3;
    public const CATEGORY_ADVERT_FEATURED_OPTION = 4;

    use EnabledTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category = null;

    #[Assert\NotBlank]
    #[ORM\Column(nullable: true)]
    private ?int $price = null;

    #[Assert\NotBlank(groups: ['Option', 'Thumbnail', 'Premium'])]
    #[ORM\Column(nullable: true)]
    private ?int $days = null;

    #[Assert\NotBlank(groups: ['Credit'])]
    #[ORM\Column(nullable: true)]
    private ?int $amount = null;

    #[Assert\NotBlank(groups: ['Option'])]
    #[ORM\Column(nullable: true)]
    private ?int $options = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Item::class)]
    private Collection $items;

    #[Pure] public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

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

    public function getDays(): ?int
    {
        return $this->days;
    }

    public function setDays(?int $days): self
    {
        $this->days = $days;

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

    public function getOptions(): ?int
    {
        return $this->options;
    }

    public function setOptions(?int $options): self
    {
        $this->options = $options;

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
            $item->setProduct($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getProduct() === $this) {
                $item->setProduct(null);
            }
        }

        return $this;
    }

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->category,
            $this->price,
            $this->days,
            $this->amount,
            $this->options
        ];
    }

    public function __unserialize(array $data): void
    {
        list (
            $this->id,
            $this->category,
            $this->price,
            $this->days,
            $this->amount,
            $this->options
            ) = $data;
    }
}
