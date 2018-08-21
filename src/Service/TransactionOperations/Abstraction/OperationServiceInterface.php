<?php
declare(strict_types=1);

namespace App\Service\TransactionOperations\Abstraction;

use App\DTO\TransactionDTO;
use App\Entity\Transaction;

interface OperationServiceInterface
{
    /**
     * @param TransactionDTO $transactionDTO
     *
     * @return Transaction
     */
    public function create(TransactionDTO $transactionDTO): Transaction;
}
