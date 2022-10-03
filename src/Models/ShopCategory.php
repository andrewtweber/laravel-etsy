<?php

namespace Etsy\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Slimak\SluggedModel;

/**
 * Class ShopCategory
 *
 * @package Etsy\Models
 *
 * @property int                       $id
 * @property string                    $name
 * @property string                    $slug
 * @property int|null                  $parent_id
 * @property string                    $icon
 * @property string                    $primary_color
 * @property string                    $secondary_color
 *
 * @property string                    $url
 *
 * @property ShopCategory              parent
 * @property Collection|ShopCategory[] children
 * @property Collection|Shop[]         shops
 * @property Collection|ShopItem[]     items
 */
class ShopCategory extends SluggedModel
{
    protected $table = 'shop_categories';

    protected $fillable = [
        'name',
        'icon',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * @return BelongsToMany
     */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(config('etsy.models.shop'), 'shop_category', 'category_id', 'shop_id');
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(config('etsy.models.shop_item'), 'category_id');
    }

    /**
     * @param string|null $icon
     *
     * @return string|null
     */
    public function getIconDuotoneAttribute(): ?string
    {
        if ($this->icon === null) {
            return null;
        }

        return str_replace(['fas ', 'far '], 'fad ', $this->icon);
    }

    /**
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return '/shop/' . $this->slug;
    }

    /**
     * Load all shops in this category.
     * - This includes those with the belongsToMany relationship
     * - But also those with any items in this category
     */
    public function loadAllShops()
    {
        $this->load([
            'shops' => function ($query) {
                $query->orderBy('name');
            },
            'shops.photo',
        ]);

        $shop_class = config('etsy.models.shop');

        $shops = $shop_class::whereHas('items', function ($query) {
            $query->where('category_id', $this->id);
        })->get();

        $this->shops = $this->shops->merge($shops)
            ->sortBy(fn(Shop $shop) => strip_articles($shop->name));
    }
}
