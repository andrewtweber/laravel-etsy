<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Import unmapped taxonomies
    |--------------------------------------------------------------------------
    |
    | If this is true, any taxonomy that is not associated to a category will
    | automatically have a category created.
    */
    'import_unmapped_taxonomies' => false,

    /*
    |--------------------------------------------------------------------------
    | API Keys
    |--------------------------------------------------------------------------
    |
    | @see https://www.etsy.com/developers/your-apps
    */
    'api' => [
        'key'    => env('ETSY_API_KEY'),
        'secret' => env('ETSY_API_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | If you need to extend the Etsy classes you can do so and add your child
    | class here.
    */
    'models' => [
        // User models
        'user'          => \App\Models\User::class,

        // Shop models
        'category'      => \Etsy\Models\ShopCategory::class,
        'shop'          => \Etsy\Models\Shop::class,
        'shop_item'     => \Etsy\Models\ShopItem::class,
        'wishlist'      => \Etsy\Models\Wishlist::class,

        // Pivots
        'favorite_shop' => \Etsy\Pivots\FavoriteShop::class,
        'favorite_item' => \Etsy\Pivots\FavoriteShopItem::class,
        'wishlist_item' => \Etsy\Pivots\WishlistItem::class,
    ],

];
