<?php
declare(strict_types=1);

namespace App\Enum\Abstraction;

interface IntEnum
{
    /**
     * Returns an array of the enum properties with integer keys
     *
     * @return array
     */
    public static function getKeyValueEnum(): array;
}