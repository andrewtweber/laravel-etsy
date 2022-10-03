<?php

namespace Etsy\Pivots;

use App\Models\Shop;
use App\Models\ShopItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class FavoriteShopItem
 *
 * @package Etsy\Pivots
 *
 * @property int      $user_id
 * @property int      $shop_item_id
 * @property int      $shop_id
 * @property Carbon   $favorited_at
 *
 * @property User     user
 * @property Shop     shop
 * @property ShopItem item
 */
class FavoriteShopItem extends Pivot
{
    protected $table = 'favorite_shop_items';

    public $timestamps = false;

    protected $dates = ['favorited_at'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(ShopItem::class, 'shop_item_id');
    }
}
