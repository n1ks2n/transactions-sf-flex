<?php
declare(strict_types=1);

namespace App\DTO\Factory;

use App\DTO\Abstraction\TransactionDTO;
use App\DTO\Constants\TransactionDTOTypes;
use App\DTO\TransactionCreateDTO;
use App\DTO\TransactionUpdateDTO;
use RuntimeException;

class TransactionDTOFactory
{
    /**
     * @param string $type
     *
     * @return TransactionDTO|TransactionCreateDTO|TransactionUpdateDTO
     */
    public function make(string $type): TransactionDTO
    {
        switch ($type) {
            case TransactionDTOTypes::CREATE:
                return new TransactionCreateDTO();
            case TransactionDTOTypes::UPDATE:
                return new TransactionUpdateDTO();
        }

        throw new RuntimeException('Unknown transaction operation!');
    }
}
