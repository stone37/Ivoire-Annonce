<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ThreadRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ThreadRepository::class)]
class Thread
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'thread', targetEntity: ThreadMetadata::class, cascade: ['persist', 'remove'])]
    private Collection $metadata;

    #[ORM\OneToMany(mappedBy: 'thread', targetEntity: ThreadMessage::class, cascade: ['persist', 'remove'])]
    private Collection $messages;

    #[ORM\ManyToOne(inversedBy: 'threads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\ManyToOne(inversedBy: 'threads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Advert $advert = null;

    protected User|Collection|null $participants = null;

    #[Pure] public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->metadata = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, ThreadMetadata>
     */
    public function getMetadata(): Collection
    {
        return $this->metadata;
    }

    public function addMetadata(ThreadMetadata $metadata): self
    {
        if (!$this->metadata->contains($metadata)) {
            $this->metadata[] = $metadata;
            $metadata->setThread($this);
        }

        return $this;
    }

    public function removeMetadata(ThreadMetadata $metadata): self
    {
        if ($this->metadata->removeElement($metadata)) {
            // set the owning side to null (unless already changed)
            if ($metadata->getThread() === $this) {
                $metadata->setThread(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ThreadMessage>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(ThreadMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setThread($this);
        }

        return $this;
    }

    public function removeMessage(ThreadMessage $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getThread() === $this) {
                $message->setThread(null);
            }
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

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

    public function getFirstMessage()
    {
        return $this->getMessages()->first();
    }

    public function getLastMessage()
    {
        return $this->getMessages()->last();
    }

    public function isDeletedByParticipant(User|UserInterface $user): bool
    {
        if ($meta = $this->getMetadataForParticipant($user)) {
            return $meta->getIsDeleted();
        }

        return false;
    }

    public function setIsDeletedByParticipant(User $user, $isDeleted)
    {
        if (!$meta = $this->getMetadataForParticipant($user)) {
            throw new InvalidArgumentException(sprintf('No metadata exists for participant with id "%s"', $user->getId()));
        }

        $meta->setIsDeleted($isDeleted);

        if ($isDeleted) {
            // also mark all thread messages as read
            foreach ($this->getMessages() as $message) {
                $message->setIsReadByParticipant($user, true);
            }
        }
    }

    public function setIsDeleted($isDeleted)
    {
        foreach ($this->getParticipants() as $participant) {
            $this->setIsDeletedByParticipant($participant, $isDeleted);
        }
    }

    public function isReadByParticipant(User|UserInterface $user): bool
    {
        foreach ($this->getMessages() as $message) {
            if (!$message->isReadByParticipant($user)) {
                return false;
            }
        }

        return true;
    }

    public function setIsReadByParticipant(User $user, $isRead): void
    {
        foreach ($this->getMessages() as $message) {
            $message->setIsReadByParticipant($user, $isRead);
        }
    }

    public function addParticipant(User $user): void
    {
        if (!$this->isParticipant($user)) {
            $this->getParticipantsCollection()->add($user);
        }
    }

    public function getParticipants(): array
    {
        return $this->getParticipantsCollection()->toArray();
    }

    public function getMetadataForParticipant(User $user)
    {
        foreach ($this->metadata as $meta) {
            if ($meta->getParticipant()->getId() == $user->getId()) {
                return $meta;
            }
        }

        return null;
    }

    public function isParticipant(User $user): bool
    {
        return $this->getParticipantsCollection()->contains($user);
    }

    protected function getParticipantsCollection(): ArrayCollection|Collection|User
    {
        if (null === $this->participants) {
            $this->participants = new ArrayCollection();

            foreach ($this->metadata as $data) {
                $this->participants->add($data->getParticipant());
            }
        }

        return $this->participants;
    }
}
