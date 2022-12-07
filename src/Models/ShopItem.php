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

/**
 * Class ShopItem
 *
 * @package Etsy\Models
 *
 * @property int                            $id
 * @property int|null                       $shop_id
 * @property int|null                       $category_id
 * @property string                         $name
 * @property string                         $original_name
 * @property string                         $slug
 * @property string                         $url - The external URL
 * @property int                            $photo_id
 * @property string|null                    $description
 * @property int|null                       $etsy_id
 * @property int                            $weight
 * @property Carbon                         $created_at
 * @property Carbon                         $updated_at
 * @property Carbon|null                    $deleted_at
 *
 * @property string                         $tracked_url
 * @property string                         $internal_url
 * @property string                         $domain
 * @property HtmlString                     $description_html
 *
 * @property Shop|null                      shop
 * @property ShopCategory|null              category
 * @property Photo|null                     photo
 * @property Collection|ShopItemStats[]     stats
 * @property Collection|EtsyUserInterface[] favoritedByUsers
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
        return $this->morphToMany(config('etsy.models.wishlist'), 'entity', 'wishlist_items')
            ->using(config('etsy.models.wishlist_item'))
            ->withPivot([
                'weight',
                'added_at',
            ]);
    }

    /**
     * @return BelongsToMany
     */
    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(config('etsy.models.user'), 'favorite_shop_items', 'shop_item_id', 'user_id')
            ->using(config('etsy.models.favorite_item'))
            ->withPivot([
                'shop_id',
                'favorited_at',
            ]);
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
        if ($this->domain === 'Barnes & Noble') {
            return "Buy at {$this->domain}";
        }

        return "Buy on {$this->domain}";
    }

    /**
     * @return string|null
     */
    public function getDomainAttribute(): ?string
    {
        $domain = parse_domain($this->url);

        if ($domain === 'prf.hn') {
            return 'Chewy.com';
        } elseif ($domain === 'barnesandnoble.com') {
            return 'Barnes & Noble';
        }

        return $domain === null ? null : ucwords($domain);
    }

    /**
     * @return string
     */
    public function getButtonClassAttribute(): string
    {
        return match ($this->domain) {
            'Amazon.com' => 'amazon',
            'Barnes & Noble' => 'bn',
            'Chewy.com' => 'chewy',
            'Etsy.com' => 'etsy',
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
