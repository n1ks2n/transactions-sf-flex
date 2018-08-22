<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Enum\TransactionStatusEnum;
use App\Enum\TransactionTypeEnum;

class AccountBalanceCalculator
{
    /**
     * @param Account $account
     *
     * @param Transaction $transaction
     *
     * @return Account
     */
    public function calculate(Account $account, Transaction $transaction): Account
    {
        $transactionStatus = $transaction->getStatus();
        $blockedBalance = $account->getBlockedBalance();
        $activeBalance = $account->getActiveBalance();

        if ($transactionStatus === TransactionStatusEnum::CREATED ||
            $transactionStatus === TransactionStatusEnum::PROCESSING
        ) {
            $account->setBlockedBalance($blockedBalance + abs($transaction->getAmount()));

            if ($transaction->getType() === TransactionTypeEnum::DEBIT) {
                $account->setActiveBalance($activeBalance + $transaction->getAmount());
            }

            return $account;
        }

        $account->setBlockedBalance($blockedBalance - abs($transaction->getAmount()));
        $account->setActiveBalance($activeBalance + $transaction->getAmount());

        return $account;
    }
}
