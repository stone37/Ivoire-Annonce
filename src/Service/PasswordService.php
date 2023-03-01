<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Data\PasswordResetRequestData;
use App\Entity\PasswordResetToken;
use App\Event\PasswordRecoveredEvent;
use App\Event\PasswordResetTokenCreatedEvent;
use App\Exception\OngoingPasswordResetException;
use App\Repository\PasswordResetTokenRepository;
use App\Security\TokenGeneratorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordService
{
    const EXPIRE_IN = 30; // Temps d'expiration d'un token

    private UserRepository $userRepository;
    private PasswordResetTokenRepository $tokenRepository;
    private EntityManagerInterface $em;
    private TokenGeneratorService $generator;
    private EventDispatcherInterface $dispatcher;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetTokenRepository $tokenRepository,
        TokenGeneratorService $generator,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->em = $em;
        $this->generator = $generator;
        $this->dispatcher = $dispatcher;
        $this->passwordHasher = $passwordHasher;
    }

    public function resetPassword(PasswordResetRequestData $data): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => $data->getEmail(), 'bannedAt' => null]);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        /** @var PasswordResetToken|null $token */
        $token = $this->tokenRepository->findOneBy(['user' => $user]);

        if (null !== $token && !$this->isExpired($token)) {
            throw new OngoingPasswordResetException();
        }

        if (null === $token) {
            $token = new PasswordResetToken();
            $this->em->persist($token);
        }

        $token->setOwner($user)
            ->setCreatedAt(new DateTime())
            ->setToken($this->generator->generate());

        $this->em->flush();

        $this->dispatcher->dispatch(new PasswordResetTokenCreatedEvent($token));
    }

    public function isExpired(PasswordResetToken $token): bool
    {
        $expirationDate = new DateTime('-'.self::EXPIRE_IN.' minutes');

        return $token->getCreatedAt() < $expirationDate;
    }

    public function updatePassword(string $password, PasswordResetToken $token): void
    {
        $user = $token->getOwner();
        $user->setConfirmationToken(null);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $this->em->remove($token);
        $this->em->flush();
        $this->dispatcher->dispatch(new PasswordRecoveredEvent($user));
    }
}
