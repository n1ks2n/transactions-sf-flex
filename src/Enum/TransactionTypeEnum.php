<?php
declare(strict_types=1);

namespace App\Enum;

use App\Enum\Abstraction\IntEnum;

class TransactionTypeEnum implements IntEnum
{
    public const DEBIT = 'debit';

    public const CREDIT = 'credit';

    /**
     * Returns an array of the enum properties with integer keys
     *
     * @return array
     */
    public static function getKeyValueEnum(): array
    {
        return [
            1 => self::DEBIT,
            2 => self::CREDIT,
        ];
    }
}