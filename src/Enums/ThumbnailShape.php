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

    case Circle  = 'circle';
    case Square  = 'square';
    case Rounded = 'rounded';
}
