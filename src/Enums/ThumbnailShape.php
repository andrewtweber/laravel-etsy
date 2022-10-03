<?php

namespace Etsy\Enums;

/**
 * Enum ThumbnailShape
 *
 * @package Etsy\Enums
 */
enum ThumbnailShape: string
{
    use EnumValues;

    public const Circle  = 'circle';
    public const Square  = 'square';
    public const Rounded = 'rounded';
}
