<?php

namespace Etsy\Models;

use Carbon\Carbon;
use Etsy\EtsyUserInterface;
use Etsy\Pivots\WishlistItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Slimak\SluggedModel;

/**
 * Class Wishlist
 *
 * @package Etsy\Models
 *
 * @property int               $id
 * @property int               $user_id
 * @property string            $name
 * @property string            $slug
 * @property string            $description
 * @property Carbon            $created_at
 * @property Carbon            $updated_at
 * @property Carbon            $deleted_at
 *
 * @property string            $url
 *
 * @property EtsyUserInterface user
 * @property Collection        shopItems
 */
class Wishlist extends SluggedModel
{
    use SoftDeletes;

    protected $table = 'wishlists';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('etsy.user.model'));
    }

    /**
     * @return MorphToMany
     */
    public function shopItems(): MorphToMany
    {
        return $this->morphedByMany(ShopItem::class, 'entity', 'wishlist_items')
            ->using(WishlistItem::class)
            ->withPivot([
                'weight',
                'added_at',
            ])
            ->orderBy('weight', 'asc');
    }

    /**
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return '/lists/' . $this->user->getRouteKey() . '/' . $this->slug;
    }
}
