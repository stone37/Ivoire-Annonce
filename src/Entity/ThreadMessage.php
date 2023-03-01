<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ThreadMessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ThreadMessageRepository::class)]
class ThreadMessage
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Veuillez renseigne votre message')]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $body = null;

    #[ORM\ManyToOne]
    private ?User $sender = null;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: ThreadMessageMetadata::class, cascade: ['persist', 'remove'])]
    private Collection $metadata;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Thread $thread = null;

    #[Pure] public function __construct()
    {
        $this->metadata = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return Collection<int, ThreadMessageMetadata>
     */
    public function getMetadata(): Collection
    {
        return $this->metadata;
    }

    public function addMetadata(ThreadMessageMetadata $metadata): self
    {
        if (!$this->metadata->contains($metadata)) {
            $this->metadata[] = $metadata;
            $metadata->setMessage($this);
        }

        return $this;
    }

    public function removeMetadata(ThreadMessageMetadata $metadata): self
    {
        if ($this->metadata->removeElement($metadata)) {
            // set the owning side to null (unless already changed)
            if ($metadata->getMessage() === $this) {
                $metadata->setMessage(null);
            }
        }

        return $this;
    }

    public function getThread(): ?Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function getMetadataForParticipant(User $participant)
    {
        foreach ($this->metadata as $meta) {
            if ($meta->getParticipant()->getId() == $participant->getId()) {
                return $meta;
            }
        }

        return null;
    }

    public function isReadByParticipant(User $user): bool
    {
        if ($meta = $this->getMetadataForParticipant($user)) {
            return $meta->isIsRead();
        }

        return false;
    }

    public function setIsReadByParticipant(User $user, $isRead)
    {
        if (!$meta = $this->getMetadataForParticipant($user)) {
            throw new InvalidArgumentException(sprintf('No metadata exists for participant with id "%s"', $user->getId()));
        }

        $meta->setIsRead($isRead);
    }

    public function getTimestamp(): ?int
    {
        return $this->getCreatedAt() ? $this->getCreatedAt()->getTimestamp() : null;
    }
}
