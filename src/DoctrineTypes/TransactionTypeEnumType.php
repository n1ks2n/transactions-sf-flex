<?php
declare(strict_types=1);

namespace App\DoctrineTypes;

use App\DoctrineTypes\Abstraction\BaseIntEnumType;
use App\Enum\TransactionTypeEnum;

class TransactionTypeEnumType extends BaseIntEnumType
{

    /**
     * @return array
     */
    public function getKeyValueEnum(): array
    {
        return TransactionTypeEnum::getKeyValueEnum();
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'transaction_type_enum';
    }
}