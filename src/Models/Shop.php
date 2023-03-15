<?php

namespace Etsy\Models;

use Carbon\Carbon;
use Etsy\Enums\Etsy\ListingState;
use Etsy\Enums\ShopStatus;
use Etsy\EtsyUserInterface;
use Etsy\Exceptions\MissingTaxonomyException;
use Etsy\Pivots\FavoriteShopItem;
use Etsy\Etsy;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HtmlString;
use Proste\Exceptions\Http404NotFoundException;
use Slimak\SluggedModel;
use Snaccs\Casts\Website;
use Snaccs\Support\Url;

/**
 * Class Shop
 *
 * @package Etsy\Models
 *
 * @property int                            $id
 * @property int|null                       $user_id
 * @property string                         $name
 * @property string                         $slug
 * @property ShopStatus                     $status
 * @property Url                            $website
 * @property string                         $logo_shape
 * @property string|null                    $description
 * @property string|null                    $country
 * @property bool|null                      $international_shipping
 * @property int|null                       $etsy_id
 * @property Carbon                         $created_at
 * @property Carbon                         $updated_at
 * @property Carbon|null                    $deleted_at
 *
 * @property string                         $url
 * @property string                         $tracked_url
 * @property string                         $base_domain
 * @property string                         $domain
 * @property HtmlString                     $description_html
 * @property HtmlString|null                $country_emoji
 *
 * @property EtsyUserInterface              user
 * @property Collection|ShopCategory[]      categories
 * @property Collection|ShopItem[]          items
 * @property Collection|EtsyUserInterface[] favoritedByUsers
 * @property Collection|FavoriteShopItem[]  favoritedItems
 */
class Shop extends SluggedModel
{
    use HasShopStats, SoftDeletes;

    protected $table = 'shops';

    protected $fillable = [
        'user_id',
        'name',
        'website',
        'photo_id',
        'logo_shape',
        'description',
        'international_shipping',
    ];

    protected $casts = [
        'international_shipping' => 'boolean',
        'status'                 => ShopStatus::class,
        'website'                => Website::class,
    ];

    public $timestamps = true;

    protected array $reserved_slugs = ['contact', 'stats'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('etsy.models.user'), 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(config('etsy.models.category'), 'shop_category', 'shop_id', 'category_id');
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(config('etsy.models.shop_item'), 'shop_id');
    }

    /**
     * @return BelongsToMany
     */
    public function favoritedByUsers(): BelongsToMany
    {
        $pivot = config('etsy.models.favorite_shop');

        return $this->belongsToMany(config('etsy.models.user'), 'favorite_shops', 'shop_id', 'user_id')
            ->using($pivot)
            ->withPivot($pivot::pivotFields());
    }

    /**
     * @return HasMany
     */
    public function favoritedItems(): HasMany
    {
        return $this->hasMany(config('etsy.models.favorite_item'), 'shop_id');
    }

    /**
     * @return ShopCategory|null
     */
    public function primaryCategory(): ?ShopCategory
    {
        if (! $this->items->count()) {
            return $this->categories[0] ?? null;
        }

        $primaryGroup = $this->items
            ->groupBy('category_id')
            ->sortByDesc(function ($group) {
                return $group->count();
            })
            ->first();

        return $primaryGroup[0]->category;
    }

    /**
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return '/shops/' . $this->slug;
    }

    /**
     * @return string
     */
    public function getButtonTextAttribute(): string
    {
        if ($this->base_domain === 'barnesandnoble.com') {
            return "Shop at Barnes & Noble";
        }

        return 'Shop on ' . ucwords($this->base_domain);
    }

    /**
     * @return string|null
     */
    public function getBaseDomainAttribute(): ?string
    {
        $url = new Url($this->website);

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
        $url = new Url($this->website);

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
     * @return HtmlString|null
     */
    public function getCountryEmojiAttribute(): ?HtmlString
    {
        if (! $this->country) {
            return null;
        }

        return new HtmlString(
            mb_convert_encoding('&#' . (127397 + ord($this->country[0])) . ';', 'UTF-8', 'HTML-ENTITIES') .
            mb_convert_encoding('&#' . (127397 + ord($this->country[1])) . ';', 'UTF-8', 'HTML-ENTITIES')
        );
    }

    /**
     * @return string
     */
    public function getTrackedUrlAttribute(): string
    {
        return $this->url . '/to?website&url=' . urlencode($this->website);
    }

    /**
     * Get listings from Etsy
     *
     * @param bool $force
     */
    public function getListingsFromEtsy(bool $force = false)
    {
        if (! $this->etsy_id) {
            return;
        }

        // If inactive, skip. Unless we are forcing it
        if (! $force && $this->status !== ShopStatus::Active) {
            return;
        }

        try {
            $listings = (new Etsy())->getListings($this);
        } catch (Http404NotFoundException $e) {
            // Etsy returns 404, means shop has closed
            $this->status = ShopStatus::Closed;
            $this->save();

            return;
        }

        // No listings in shop. Switch them to inactive so that we will stop trying to fetch
        if (! isset($listings['results']) || count($listings['results']) === 0) {
            $this->status = ShopStatus::Inactive;
            $this->save();

            return;
        }

        $new_or_updated_ids = [];

        $counter = 1;
        foreach ($listings['results'] ?? [] as $result) {
            // Note these are all active (we can only fetch active items from the API)
            // But if we ever used OAuth we'd have access to inactive listings too
            if ($result['state'] !== ListingState::Active->value) {
                continue;
            }

            // If they ranked their featured items, otherwise just in the order they are retrieved
            $weight = $result['featured_rank'] === -1 ? $counter : $result['featured_rank'];

            $item_class = config('etsy.models.shop_item');

            /** @var ShopItem $item */
            $item = $item_class::withTrashed()->firstOrNew([
                'shop_id' => $this->id,
                'etsy_id' => $result['listing_id'],
            ]);

            // Item has already been imported and deleted
            if ($item->deleted_at) {
                continue;
            }

            // Map taxonomy to category if it isn't set already
            // TODO: option to force Etsy's value to overwrite the existing one?
            if (! $item->category_id) {
                /** @var EtsyTaxonomy $original_taxonomy */
                $original_taxonomy = EtsyTaxonomy::where('etsy_taxonomy_id', $result['taxonomy_id'])->first();
                $taxonomy = $original_taxonomy;

                if (! $taxonomy) {
                    throw new MissingTaxonomyException();
                }

                do {
                    if ($taxonomy->shop_category_id) {
                        $item->category_id = $taxonomy->shop_category_id;
                        break;
                    } else {
                        $taxonomy = $taxonomy->parent;
                    }
                } while ($taxonomy !== null);

                // No category found - create one (and parents) if setting is enabled.
                if (! $item->category_id && config('etsy.import_unmapped_taxonomies')) {
                    $taxonomy = $original_taxonomy;
                    $hierarchy = [];

                    do {
                        $category = $taxonomy->shopCategory;

                        if (! $category) {
                            // Even if we just used `create`, it shouldn't create duplicates, but just in case.
                            $category = ShopCategory::firstOrCreate([
                                'name' => $taxonomy->name,
                            ]);
                            $taxonomy->shop_category_id = $category->id;
                            $taxonomy->save();
                        }

                        $hierarchy[] = $category;
                        $taxonomy = $taxonomy->parent;
                    } while ($taxonomy !== null);

                    $parent = null;
                    foreach (array_reverse($hierarchy) as $category) {
                        $category->parent_id = $parent?->id;
                        $category->save();
                        $parent = $category;
                    }

                    $original_taxonomy->refresh();
                    $item->category_id = $original_taxonomy->shop_category_id;
                }
            }

            $old_original = $item->original_name;

            $item->fill([
                'original_name' => html_entity_decode($result['title']),
                'description'   => html_entity_decode($result['description']),
                'url'           => $result['url'],
                'weight'        => $weight,
            ]);

            // Fields that may have been updated on FerretLove that we don't want to change
            if (! $item->exists || $item->name === $old_original) {
                $item->name = html_entity_decode($result['title']);
            }

            $item->save();
            $item->addToIndex();

            $counter++;
            $new_or_updated_ids[] = $item->id;
        }

        // Items that are no longer active should be deleted
        $items_to_delete = $this->items()
            ->whereNotNull('etsy_id')
            ->whereNotIn('id', $new_or_updated_ids);

        foreach ($items_to_delete->cursor() as $item) {
            $item->delete();
        }
    }
}
