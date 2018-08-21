<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Enum\TransactionStatusEnum;

class AccountBalanceCalculator
{
    public function calculate(Account $account, Transaction $transaction): Account
    {
        $transactionStatus = $transaction->getStatus();

        if ($transactionStatus === TransactionStatusEnum::CREATED ||
            $transactionStatus === TransactionStatusEnum::PROCESSING
        ) {
            $blockedBalance = $account->getBlockedBalance();
            $account->setBlockedBalance($blockedBalance + $transaction->getAmount());
        } else {
            $activeBalance = $account->getActiveBalance();
            $account->setActiveBalance($activeBalance + $transaction->getAmount());
        }

        $totalBalance = $account->getTotalBalance();
        $account->setTotalBalance($totalBalance + $transaction->getAmount());

        return $account;
    }
}
