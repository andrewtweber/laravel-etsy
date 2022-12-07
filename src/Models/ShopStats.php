<?php

namespace Etsy\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ShopStats
 *
 * @package Etsy\Models
 *
 * @property int    $id
 * @property int    $shop_id
 * @property Carbon $date
 * @property int    $views
 * @property int    $website_clicks
 *
 * @property Shop   shop
 */
class ShopStats extends Model
{
    protected $table = 'shop_stats';

    public $timestamps = false;

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(config('etsy.models.shop'), 'shop_id');
    }

    /**
     * @return string
     */
    public function getLabelAttribute(): string
    {
        // TODO: This is gross
        // If it's length 6, it's the YEARMONTH field
        // Otherwise it's the date field, but we still rename it to "month" for the Blade template
        if (isset($this->month)) {
            if (strlen($this->month) === 6) {
                return Carbon::createFromFormat('Ymd', $this->month . '01')->format('F Y');
            } else {
                return Carbon::createFromFormat('Y-m-d', $this->month)->format('M j, Y');
            }
        }

        return $this->date->startOfMonth()->format('F Y');
    }
}
