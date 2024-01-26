<?php

namespace Etsy\Models;

use Etsy\Etsy;
use Etsy\EtsyUserInterface;
use Etsy\Events\ShopItemPhotoFetched;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Slimak\SluggedModel;
use Snaccs\Support\Url;

/**
 * Class ShopItem
 *
 * @package Etsy\Models
 *
 * @property int                           $id
 * @property ?int                          $shop_id
 * @property ?int                          $category_id
 * @property string                        $name
 * @property ?string                       $original_name
 * @property string                        $slug
 * @property string                        $url - The external URL
 * @property ?int                          $photo_id
 * @property ?string                       $description
 * @property ?int                          $price - The minimum price
 * @property ?string                       $currency
 * @property ?int                          $etsy_id
 * @property int                           $weight
 * @property Carbon                        $created_at
 * @property Carbon                        $updated_at
 * @property ?Carbon                       $deleted_at
 *
 * @property string                        $tracked_url
 * @property string                        $internal_url
 * @property string                        $base_domain
 * @property string                        $domain
 * @property HtmlString                    $description_html
 *
 * @property ?Shop                         $shop
 * @property ?ShopCategory                 $category
 * @property ?Photo                        $photo
 * @property Collection<ShopItemStats>     $stats
 * @property Collection<EtsyUserInterface> $favoritedByUsers
 */
class ShopItem extends SluggedModel
{
    use SoftDeletes;

    protected $table = 'shop_items';

    protected $fillable = [
        'shop_id',
        'name',
        'original_name',
        'url',
        'photo_id',
        'description',
        'price',
        'currency',
        'etsy_id',
        'weight',
    ];

    public $timestamps = true;

    protected array $reserved_slugs = ['stats', 'to'];

    /**
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(config('etsy.models.shop'), 'shop_id');
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(config('etsy.models.category'), 'category_id');
    }

    /**
     * @return HasMany
     */
    public function stats(): HasMany
    {
        return $this->hasMany(ShopItemStats::class, 'shop_item_id');
    }

    /**
     * @return MorphToMany
     */
    public function wishlists(): MorphToMany
    {
        $pivot = config('etsy.models.wishlist_item');

        return $this->morphToMany(config('etsy.models.wishlist'), 'entity', 'wishlist_items')
            ->using($pivot)
            ->withPivot($pivot::pivotFields());
    }

    /**
     * @return BelongsToMany
     */
    public function favoritedByUsers(): BelongsToMany
    {
        $pivot = config('etsy.models.favorite_item');

        return $this->belongsToMany(config('etsy.models.user'), 'favorite_shop_items', 'shop_item_id', 'user_id')
            ->using($pivot)
            ->withPivot($pivot::pivotFields());
    }

    /**
     * @return string
     */
    public function getTrackedUrlAttribute(): string
    {
        return $this->internal_url . '/to?website&url=' . urlencode($this->url);
    }

    /**
     * @return string
     */
    public function getInternalUrlAttribute(): string
    {
        return $this->shop->url . '/' . $this->slug;
    }

    /**
     * @return string
     */
    public function getButtonTextAttribute(): string
    {
        if ($this->base_domain === 'barnesandnoble.com') {
            return "Buy at Barnes & Noble";
        }

        return 'Buy on ' . ucwords($this->base_domain);
    }

    /**
     * @return string|null
     */
    public function getBaseDomainAttribute(): ?string
    {
        $url = new Url($this->url);

        if ($url->base_domain === 'prf.hn') {
            return 'chewy.com';
        }

        return strtolower($url->base_domain);
    }

    /**
     * @return string|null
     */
    public function getDomainAttribute(): ?string
    {
        $url = new Url($this->url);

        if ($url->domain === 'prf.hn') {
            return 'chewy.com';
        }

        return strtolower($url->domain);
    }

    /**
     * @return string
     */
    public function getButtonClassAttribute(): string
    {
        return match ($this->base_domain) {
            'amazon.com' => 'amazon',
            'barnesandnoble.com' => 'bn',
            'chewy.com' => 'chewy',
            'etsy.com' => 'etsy',
            default => 'primary',
        };
    }

    /**
     * @return HtmlString
     */
    public function getDescriptionHtmlAttribute(): HtmlString
    {
        return new HtmlString(nl2br(e($this->description)));
    }

    /**
     * @return bool
     */
    public function isSponsored(): bool
    {
        return Str::contains($this->url, 'prf.hn');
    }

    /**
     * Get photo from Etsy
     */
    public function getPhotoFromEtsy()
    {
        if ($this->photo_id) {
            return;
        }

        if (! $this->etsy_id) {
            return;
        }

        $details = (new Etsy())->getListingDetails($this);

        if (count($details['images']) === 0) {
            return;
        }

        foreach ($details['images'] as $image) {
            $url = $image['url_fullxfull'] ?? null;

            if (! $url) {
                continue;
            }

            // Save the first photo
            // TODO: save additional photos
            ShopItemPhotoFetched::dispatch($this, $url, $image['listing_image_id'] ?? null);

            break;
        }
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->internal_url;
    }

    /**
     * @return string
     */
    public function canonicalUrl(): ?string
    {
        return url($this->internal_url);
    }
}
