<?php

namespace Etsy\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ShopItemStats
 *
 * @package Etsy\Models
 *
 * @property int      $id
 * @property int      $shop_id
 * @property int      $shop_item_id
 * @property Carbon   $date
 * @property int      $views
 * @property int      $website_clicks
 *
 * @property Shop     shop
 * @property ShopItem item
 */
class ShopItemStats extends Model
{
    protected $table = 'shop_item_stats';

    public $timestamps = false;

    protected $casts = [
        'date' => 'date',
    ];

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

    /**
     * @return string
     */
    public function getLabelAttribute(): string
    {
        if (isset($this->month)) {
            return Carbon::createFromFormat('Ymd', $this->month . '01')->format('F Y');
        }

        return $this->date->startOfMonth()->format('F Y');
    }
}
