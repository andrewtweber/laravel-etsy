<?php

namespace Etsy\Enums;

/**
 * Enum ShopStatus
 *
 * @package Etsy\Enums
 */
enum ShopStatus: string
{
    use EnumValues;

    case Active = 'active';
    case Inactive = 'inactive';
    case Vacation = 'vacation';
    case Closed = 'closed';
}
