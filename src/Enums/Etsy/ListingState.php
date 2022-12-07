<?php

namespace Etsy\Enums\Etsy;

/**
 * Enum ListingState
 *
 * @package Etsy\Enums\Etsy
 *
 * @see https://www.etsy.com/developers/documentation/reference/listing#section_listing_states
 */
enum ListingState: string
{
    case Active = 'active';
    case Removed = 'removed';
    case SoldOut = 'sold_out';
    case Expired = 'expired';
    case Inactive = 'edit'; // For legacy reasons, this displays as "edit"
    case Draft = 'draft';
    case Private = 'private';
    case Unavailable = 'unavailable';
}
