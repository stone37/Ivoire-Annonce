<?php

namespace App\Entity;

use App\Entity\Traits\EnabledTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Repository\AlertRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: AlertRepository::class)]
class Alert
{
    use EnabledTrait;
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category;

    #[ORM\ManyToOne]
    private ?Category $subCategory;

    #[ORM\ManyToOne(inversedBy: 'alerts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User|UserInterface|null $owner
     * @return $this
     */
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
