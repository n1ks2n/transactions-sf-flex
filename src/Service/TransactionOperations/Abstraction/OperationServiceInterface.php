<?php
declare(strict_types=1);

namespace App\Service\TransactionOperations\Abstraction;

use App\DTO\TransactionCreateDTO;
use App\Entity\Transaction;

interface OperationServiceInterface
{
    /**
     * @param TransactionCreateDTO $transactionDTO
     *
     * @return Transaction
     */
    public function create(TransactionCreateDTO $transactionDTO): Transaction;
}
