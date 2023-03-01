<?php

namespace App\Entity;

use App\Repository\ThreadMetadataRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThreadMetadataRepository::class)]
class ThreadMetadata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isDeleted = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $lastParticipantMessageDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $lastMessageDate = null;

    #[ORM\ManyToOne]
    private ?User $participant = null;

    #[ORM\ManyToOne(targetEntity: Thread::class, inversedBy: 'metadata')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Thread $thread = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getLastParticipantMessageDate(): ?DateTimeInterface
    {
        return $this->lastParticipantMessageDate;
    }

    public function setLastParticipantMessageDate(?DateTimeInterface $lastParticipantMessageDate): self
    {
        $this->lastParticipantMessageDate = $lastParticipantMessageDate;

        return $this;
    }

    public function getLastMessageDate(): ?DateTimeInterface
    {
        return $this->lastMessageDate;
    }

    public function setLastMessageDate(?DateTimeInterface $lastMessageDate): self
    {
        $this->lastMessageDate = $lastMessageDate;

        return $this;
    }

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?User $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setThread(Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }
}
