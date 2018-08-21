<?php
declare(strict_types=1);

namespace App\Enum;

use App\Enum\Abstraction\IntEnum;

class TransactionStatusEnum implements IntEnum
{
    public const CREATED = 'created';

    public const PROCESSING = 'processing';

    public const PROCESSED = 'processed';

    public const ERROR = 'error';

    /**
     * Returns an array of the enum properties with integer keys
     *
     * @return array
     */
    public static function getKeyValueEnum(): array
    {
        return [
            1 => self::CREATED,
            2 => self::PROCESSING,
            3 => self::PROCESSED,
            4 => self::ERROR,
        ];
    }
}
