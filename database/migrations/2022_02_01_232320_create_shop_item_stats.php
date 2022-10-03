<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopItemStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_item_stats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('shop_item_id')->index();
            $table->date('date')->index();
            $table->unsignedInteger('views')->default(0)->index();
            $table->unsignedInteger('website_clicks')->default(0);

            $table->unique(['shop_item_id', 'date']);

            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('shop_item_id')->references('id')->on('shop_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_item_stats');
    }
}
