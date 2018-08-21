<?php
declare(strict_types=1);

namespace App\DoctrineTypes;

use App\DoctrineTypes\Abstraction\BaseIntEnumType;
use App\Enum\TransactionStatusEnum;

class TransactionStatusEnumType extends BaseIntEnumType
{

    /**
     * @return array
     */
    public function getKeyValueEnum(): array
    {
        return TransactionStatusEnum::getKeyValueEnum();
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'transaction_status_enum';
    }
}