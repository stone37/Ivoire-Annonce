<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Wallet;
use App\Entity\WalletHistory;
use App\Exception\NotEnoughMoneyException;
use Doctrine\ORM\EntityManagerInterface;

class TransactionService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function addMoney(User $user, int $amount, string $name, ?string $description = null, array $data = [])
    {
        $wallet = $user->getWallet();
        $wallet->setTotalAmount($wallet->getTotalAmount() + $amount);
        $history = $this->addWalletHistory($wallet, WalletHistory::TYPE_INCOME, $amount, $name, $description, $data);

        $this->em->persist($history);
        $this->em->flush();
    }

    public function subMoney(User $user, int $amount, string $name, ?string $description = null, array $data = [])
    {
        $wallet = $user->getWallet();

        if (false === $wallet->isAvailableMoney($amount)) {
            throw new NotEnoughMoneyException;
        }

        $wallet->setTotalAmount($wallet->getTotalAmount() - $amount);
        $history = $this->addWalletHistory($wallet, WalletHistory::TYPE_SALARY, $amount, $name, $description, $data);

        $this->em->persist($history);
        $this->em->flush();
    }

    public function freezeMoney(User $user, int $amount, string $name, ?string $description = null, array $data = [])
    {
        $wallet = $user->getWallet();

        $wallet->setFreezeAmount($wallet->getFreezeAmount() + $amount);
        $history = $this->addWalletHistory($wallet, WalletHistory::TYPE_RECLAMATION, $amount, $name, $description, $data);

        $this->em->persist($history);
        $this->em->flush();
    }

    public function moveFreezeMoneyToUser(User $fromUser, User $toUser, int $amount, string $name, ?string $description = null, array $data = [])
    {
        $fromWallet = $fromUser->getWallet();
        $toWallet = $toUser->getWallet();

        if (false === $fromWallet->isAvailableMoney($amount, Wallet::FREEZE_MONEY)) {
            throw new NotEnoughMoneyException;
        }

        $fromWallet->setTotalAmount($fromWallet->getTotalAmount() - $amount);
        $fromWallet->setFreezeAmount($fromWallet->getFreezeAmount() - $amount);
        $toWallet->setTotalAmount($toWallet->getTotalAmount() + $amount);
        $history = $this->addWalletHistory($toWallet, WalletHistory::TYPE_INCOME, $amount, $name, $description, $data);

        $this->em->persist($history);
        $this->em->flush();
    }

    private function addWalletHistory(Wallet $wallet, int $type, int $amount, string $name, ?string $description = null, ?array $data = []): WalletHistory
    {
        $history = (new WalletHistory())
            ->setType($type)
            ->setAmount($amount)
            ->setName($name)
            ->setDescription($description)
            ->setData($data)
            ->setWallet($wallet);

        return $history;
    }
}