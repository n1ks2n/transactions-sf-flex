<?php
declare(strict_types=1);

namespace App\DTO\Factory;

use App\DTO\TransactionDTO;

class TransactionDTOFactory
{
    public function make(): TransactionDTO
    {
        return new TransactionDTO();
    }
}