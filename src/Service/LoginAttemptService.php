<?php

namespace App\Service;

use App\Entity\LoginAttempt;
use App\Repository\LoginAttemptRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class LoginAttemptService
{
    const ATTEMPTS = 3;

    public function __construct(
        private LoginAttemptRepository $repository,
        private EntityManagerInterface $em
    )
    {
    }

    public function addAttempt(User $user): void
    {
        // TODO : Envoyer un email au bout du Xème essai
        $attempt = (new LoginAttempt())->setOwner($user);
        $this->em->persist($attempt);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function limitReachedFor(User $user): bool
    {
        return $this->repository->countRecentFor($user, 30) >= self::ATTEMPTS;
    }
}
