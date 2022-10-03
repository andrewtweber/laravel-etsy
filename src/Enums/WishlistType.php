<?php

namespace Etsy\Enums;

/**
 * Enum WishlistType
 *
 * @package App\Support\Enums
 */
enum WishlistType: string
{
    use EnumValues;

    case Food = 'food';
    case ShopItem = 'item';
}
