<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_category', function (Blueprint $table) {
            $table->unsignedBigInteger('shop_id');
            $table->unsignedBigInteger('category_id');

            $table->primary(['shop_id', 'category_id']);
            $table->index('shop_id');
            $table->index('category_id');

            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('category_id')->references('id')->on('shop_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_category');
    }
}
