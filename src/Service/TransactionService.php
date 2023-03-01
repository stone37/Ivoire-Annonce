<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Wallet;
use App\Entity\WalletHistory;
use App\Exception\MoneyTypeNotExistException;
use App\Exception\NotEnoughMoneyException;
use App\Repository\WalletHistoryRepository;

class TransactionService
{
    public function __construct(private WalletHistoryRepository $repository)
    {
    }

    // Ajouter de l'argent au compte utilisateur
    public function addMoney(User $user, int $amount, string $name, ?string $description = null, array $data = [])
    {
        $wallet = $user->getWallet();
        $wallet->setTotalAmount($wallet->getTotalAmount() + $amount);
        $history = $this->addWalletHistory($wallet, WalletHistory::TYPE_INCOME, $amount, $name, $description, $data);

        $this->repository->add($history, true);
    }

    /**
     * Soustraire de l'argent du compte utilisateur
     *
     * @throws MoneyTypeNotExistException
     * @throws NotEnoughMoneyException
     */
    public function subMoney(User $user, int $amount, string $name, ?string $description = null, array $data = [])
    {
        $wallet = $user->getWallet();

        if (false === $wallet->isAvailableMoney($amount)) {
            throw new NotEnoughMoneyException;
        }

        $wallet->setTotalAmount($wallet->getTotalAmount() - $amount);
        $history = $this->addWalletHistory($wallet, WalletHistory::TYPE_SALARY, $amount, $name, $description, $data);

        $this->repository->add($history, true);
    }

    // Geler l'argent sur le compte d'utilisateur (le compte n'a pas besoin d'avoir assez d'argent pour le geler)
    public function freezeMoney(User $user, int $amount, string $name, ?string $description = null, array $data = [])
    {
        $wallet = $user->getWallet();

        $wallet->setFreezeAmount($wallet->getFreezeAmount() + $amount);
        $history = $this->addWalletHistory($wallet, WalletHistory::TYPE_RECLAMATION, $amount, $name, $description, $data);

        $this->repository->add($history, true);
    }

    /**
     * Déplacer l'argent gelé vers un autre compte d'utilisateur
     * Si le compte n'a pas assez d'argent gelé dans le portefeuille, la méthode lance une exception
     *
     * @throws NotEnoughMoneyException
     * @throws MoneyTypeNotExistException
     */
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

        $this->repository->add($history, true);
    }

    private function addWalletHistory(Wallet $wallet, int $type, int $amount, string $name, ?string $description = null, ?array $data = []): WalletHistory
    {
        return (new WalletHistory())
            ->setType($type)
            ->setAmount($amount)
            ->setName($name)
            ->setDescription($description)
            ->setData($data)
            ->setWallet($wallet);
    }
}