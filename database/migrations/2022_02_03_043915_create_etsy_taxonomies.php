<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtsyTaxonomies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etsy_taxonomies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('etsy_taxonomy_id')->index();
            $table->integer('etsy_parent_id')->nullable()->index();
            $table->unsignedBigInteger('shop_category_id')->nullable()->index();

            $table->foreign('shop_category_id')->references('id')->on('shop_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('etsy_taxonomies');
    }
}
