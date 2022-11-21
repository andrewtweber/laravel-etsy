<?php

namespace Etsy\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasShopStats
 *
 * @package Etsy\Models
 *
 * @property Collection|ShopStats[]     stats
 * @property Collection|ShopItemStats[] itemStats
 */
trait HasShopStats
{
    /**
     * @return HasMany
     */
    public function stats(): HasMany
    {
        return $this->hasMany(ShopStats::class);
    }

    /**
     * @return HasMany
     */
    public function itemStats(): HasMany
    {
        return $this->hasMany(ShopItemStats::class);
    }
}
