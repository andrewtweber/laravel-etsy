<?php

namespace Etsy;

use Etsy\Models\Shop;
use Etsy\Models\ShopItem;
use Etsy\Models\Wishlist;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait EtsyUser
 *
 * @package Etsy
 *
 * @property Collection|Shop[]     favoriteShops
 * @property Collection|ShopItem[] favoriteShopItems
 * @property Collection|Wishlist[] wishlists
 */
trait EtsyUser
{
    /**
     * @return HasMany
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(config('etsy.models.wishlist'));
    }

    /**
     * @return BelongsToMany
     */
    public function favoriteShops(): BelongsToMany
    {
        return $this->belongsToMany(config('etsy.models.shop'), 'favorite_shops', 'user_id', 'shop_id')
            ->using(config('etsy.models.favorite_shop'))
            ->withPivot([
                'favorited_at',
            ]);
    }

    /**
     * @return BelongsToMany
     */
    public function favoriteShopItems(): BelongsToMany
    {
        return $this->belongsToMany(config('etsy.models.shop_item'), 'favorite_shop_items', 'user_id', 'shop_item_id')
            ->using(config('etsy.models.favorite_item'))
            ->withPivot([
                'shop_id',
                'favorited_at',
            ]);
    }
}
