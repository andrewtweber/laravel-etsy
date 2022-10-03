<?php

namespace App\Nova;

use Causelabs\ResourceIndexLink\ResourceIndexLink;
use Etsy\Enums\ShopStatus;
use Etsy\Enums\ThumbnailShape;
use Inspheric\Fields\Url;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Shop extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Etsy\Models\Shop::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static $group = 'Marketplace';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    public static $with = [
        'categories',
    ];

    /**
     * @return string
     */
    public function title()
    {
        return $this->name;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            ResourceIndexLink::make('Name')
                ->sortable()
                ->rules('required', 'max:255'),

            BelongsTo::make('User')
                ->nullable(),

            Select::make('Status')
                ->options(
                    collect(ShopStatus::values())
                        ->mapWithKeys(fn ($status) => [$status => $status])
                )
                ->required(),

            Select::make('Logo Shape')
                ->options(
                    collect(ThumbnailShape::values())
                        ->mapWithKeys(fn ($shape) => [$shape => $shape])
                )
                ->hideFromIndex()
                ->required(),

            Text::make('Website', 'domain')
                ->onlyOnIndex(),

            Url::make('Website')
                ->hideFromIndex()
                ->required(),

            Textarea::make('Description')
                ->hideFromIndex()
                ->required(),

            Text::make('Country')
                ->hideFromIndex()
                ->required(),

            BelongsToMany::make('Categories', 'categories', ShopCategory::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
