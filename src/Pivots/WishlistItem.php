<?php

namespace Etsy\Pivots;

use Etsy\Models\ShopItem;
use Etsy\Models\Wishlist;
use Etsy\Enums\WishlistType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class WishlistItem
 *
 * @package Etsy\Pivots
 *
 * @property int          $wishlist_id
 * @property WishlistType $entity_type
 * @property int          $entity_id
 * @property int          $weight
 * @property Carbon       $added_at
 *
 * @property Wishlist     wishlist
 * @property ShopItem     entity
 */
class WishlistItem extends MorphPivot
{
    protected $table = 'wishlist_items';

    public $timestamps = false;

    protected $casts = [
        'entity_type' => WishlistType::class,
    ];

    protected $dates = ['added_at'];

    /**
     * @return BelongsTo
     */
    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
    }

    /**
     * @return MorphTo
     */
    public function entity(): MorphTo
    {
        return $this->morphTo();
    }
}
