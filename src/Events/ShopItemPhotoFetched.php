<?php

namespace Etsy\Events;

use Etsy\Models\ShopItem;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ShopItemPhotoFetched
 *
 * @package Etsy\Events
 */
class ShopItemPhotoFetched
{
    use Dispatchable, SerializesModels;

    /**
     * @param ShopItem $item
     * @param string   $photo_url - the full URL of the image
     * @param int      $etsy_id - the ID of the listing image
     */
    public function __construct(
        public ShopItem $item,
        public string $photo_url,
        public int $etsy_id,
    ) {
    }
}
