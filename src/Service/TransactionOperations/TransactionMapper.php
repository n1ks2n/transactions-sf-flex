<?php
declare(strict_types=1);

namespace App\Service\TransactionOperations;

use App\Entity\Transaction;

class TransactionMapper
{
    public function mapToArray(Transaction $transaction): array
    {
        return [
            'id' => $transaction->getId(),
            'amount' => $transaction->getAmount(),
            'type' => $transaction->getType(),
            'status' => $transaction->getStatus(),
            'request_id' => $transaction->getRequestId(),
            'account' => [
                'id' => $transaction->getAccount()->getId(),
                'active_balance' => $transaction->getAccount()->getActiveBalance(),
                'total_balance' => $transaction->getAccount()->getTotalBalance(),
                'blocked_balance' => $transaction->getAccount()->getBlockedBalance()
            ],
        ];
    }
}
