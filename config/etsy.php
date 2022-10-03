<?php

return [

    'api' => [
        // https://www.etsy.com/developers/your-apps
        'key'    => env('ETSY_API_KEY'),
        'secret' => env('ETSY_API_SECRET'),
    ],

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
