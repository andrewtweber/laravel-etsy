<?php

namespace Etsy\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class EtsyTaxonomy
 *
 * @package App\Models
 *
 * @property int                       $id
 * @property string                    $name
 * @property int                       $etsy_taxonomy_id
 * @property int                       $etsy_parent_id
 * @property int|null                  $shop_category_id
 *
 * @property EtsyTaxonomy              parent
 * @property ShopCategory              category
 * @property Collection|EtsyTaxonomy[] children
 */
class EtsyTaxonomy extends Model
{
    protected $fillable = [
        'name',
        'etsy_taxonomy_id',
        'etsy_parent_id',
        'shop_category_id',
    ];

    public $timestamps = false;

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(EtsyTaxonomy::class, 'etsy_parent_id', 'etsy_taxonomy_id');
    }

    /**
     * @return BelongsTo
     */
    public function shopCategory(): BelongsTo
    {
        return $this->belongsTo(config('etsy.models.category'));
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(EtsyTaxonomy::class, 'etsy_parent_id', 'etsy_taxonomy_id');
    }
}
