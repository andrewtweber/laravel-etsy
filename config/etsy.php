<?php

return [

    'models' => [
        'user' => \App\Models\User::class,

        'category'  => \Etsy\Models\ShopCategory::class,
        'shop'      => \Etsy\Models\Shop::class,
        'shop_item' => \Etsy\Models\ShopItem::class,
        'wishlist'  => \Etsy\Models\Wishlist::class,
    ],

];
