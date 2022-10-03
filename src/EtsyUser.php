<?php

namespace Etsy;

use Etsy\Models\Shop;
use Etsy\Models\ShopItem;
use Etsy\Models\Wishlist;
use Etsy\Pivots\FavoriteShop;
use Etsy\Pivots\FavoriteShopItem;
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
        return $this->hasMany(Wishlist::class);
    }

    /**
     * @return BelongsToMany
     */
    public function favoriteShops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'favorite_shops', 'user_id', 'shop_id')
            ->using(FavoriteShop::class)
            ->withPivot([
                'favorited_at',
            ]);
    }

    /**
     * @return BelongsToMany
     */
    public function favoriteShopItems(): BelongsToMany
    {
        return $this->belongsToMany(ShopItem::class, 'favorite_shop_items', 'user_id', 'shop_item_id')
            ->using(FavoriteShopItem::class)
            ->withPivot([
                'shop_id',
                'favorited_at',
            ]);
    }
}
