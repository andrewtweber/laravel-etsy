<?php

namespace Etsy\Pivots;

use Etsy\EtsyUserInterface;
use Etsy\Models\Shop;
use Etsy\Models\ShopItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class FavoriteShopItem
 *
 * @package Etsy\Pivots
 *
 * @property int               $user_id
 * @property int               $shop_item_id
 * @property int               $shop_id
 * @property Carbon            $favorited_at
 *
 * @property EtsyUserInterface user
 * @property Shop              shop
 * @property ShopItem          item
 */
class FavoriteShopItem extends Pivot
{
    protected $table = 'favorite_shop_items';

    public $timestamps = false;

    protected $casts = [
        'favorited_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('etsy.models.user'));
    }

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(config('etsy.models.shop'));
    }

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(config('etsy.models.shop_item'), 'shop_item_id');
    }

    /**
     * @return string[]
     */
    public static function pivotFields(): array
    {
        return [
            'shop_id',
            'favorited_at',
        ];
    }
}
