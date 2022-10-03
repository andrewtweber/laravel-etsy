<?php

namespace Etsy;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Interface EtsyUserInterface
 *
 * @package Etsy
 */
interface EtsyUserInterface extends UrlRoutable
{
    public function wishlists(): HasMany;

    public function favoriteShops(): BelongsToMany;

    public function favoriteShopItems(): BelongsToMany;
}
