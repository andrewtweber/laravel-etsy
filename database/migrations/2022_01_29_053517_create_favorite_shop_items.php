<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoriteShopItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('favorite_shop_items', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shop_item_id');
            $table->unsignedBigInteger('shop_id');
            $table->datetime('favorited_at');

            $table->primary(['user_id', 'shop_item_id']);
            $table->index('user_id');
            $table->index('shop_item_id');
            $table->index('shop_id');
            $table->index('favorited_at');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('shop_item_id')->references('id')->on('shop_items');
            $table->foreign('shop_id')->references('id')->on('shops');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('favorite_shop_items');
    }
}
