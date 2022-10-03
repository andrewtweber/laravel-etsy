<?php

namespace Etsy\Observers;

use Etsy\Jobs\GetShopItemPhoto;
use Etsy\Models\ShopItem;

/**
 * Class ShopItemObserver
 *
 * @package App\Observers
 */
class ShopItemObserver
{
    /**
     * @param ShopItem $item
     */
    public function saved(ShopItem $item)
    {
        if ($item->deleted_at !== null) {
            return;
        }

        if ($item->photo_id) {
            return;
        }

        dispatch_with_delay(new GetShopItemPhoto($item), delay: 5, initial_delay: 5);
    }
}
