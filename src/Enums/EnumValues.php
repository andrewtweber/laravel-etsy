<?php

namespace Etsy\Enums;

/**
 * Trait EnumValues
 *
 * @package Etsy\Enums;
 */
trait EnumValues
{
    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(
            fn($case) => $case->value,
            static::cases()
        );
    }
}
