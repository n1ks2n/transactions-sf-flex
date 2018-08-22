<?php
declare(strict_types=1);

namespace App\Service\TransactionOperations;

use App\Entity\Transaction;
use App\Enum\TransactionStatusEnum;

class TransactionStatusChecker
{
    /**
     * @param Transaction $transaction
     * @param string $status
     *
     * @return bool
     */
    public function canBeChanged(Transaction $transaction, string $status): bool
    {
        if ($transaction->getStatus() === TransactionStatusEnum::CREATED) {
            return true;
        }

        if ($status !== TransactionStatusEnum::CREATED && $transaction->getStatus() === TransactionStatusEnum::PROCESSING) {
            return true;
        }

        if ($status === TransactionStatusEnum::ERROR && $transaction->getStatus() !== TransactionStatusEnum::PROCESSED) {
            return true;
        }

        return false;
    }
}
