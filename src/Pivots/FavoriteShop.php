<?php

namespace Etsy\Pivots;

use Etsy\EtsyUserInterface;
use Etsy\Models\Shop;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class FavoriteShop
 *
 * @package Etsy\Pivots
 *
 * @property int               $shop_id
 * @property int               $user_id
 * @property Carbon            $favorited_at
 *
 * @property EtsyUserInterface user
 * @property Shop              shop
 */
class FavoriteShop extends Pivot
{
    protected $table = 'favorite_shops';

    public $timestamps = false;

    protected $dates = ['favorited_at'];

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
}
