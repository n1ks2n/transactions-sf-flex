<?php
declare(strict_types=1);

namespace App\DoctrineTypes\Abstraction;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use RuntimeException;

abstract class BaseIntEnumType extends Type
{
    /**
     * @param array             $fieldDeclaration
     * @param AbstractPlatform  $platform
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'SMALLINT';
    }

    /**
     * @param mixed             $value
     * @param AbstractPlatform  $platform
     *
     * @return int
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        return (int) array_search($value, $this->getKeyValueEnum(), true);
    }

    /**
     * @param mixed             $value
     * @param AbstractPlatform  $platform
     *
     * @return string
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): string
    {
        if (!array_key_exists($value, $this->getKeyValueEnum())) {
            throw new RuntimeException(sprintf('Cannot find value for the key: %d', $value));
        }

        return (string) $this->getKeyValueEnum()[$value];
    }

    /**
     * @return array
     */
    abstract public function getKeyValueEnum(): array;
}