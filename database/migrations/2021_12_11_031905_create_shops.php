<?php

use Etsy\Enums\ShopStatus;
use Etsy\Enums\ThumbnailShape;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('status', ShopStatus::values())->default(ShopStatus::Active->value);
            $table->text('website');
            $table->unsignedBigInteger('photo_id')->nullable()->index();
            $table->enum('logo_shape', ThumbnailShape::values())->default(ThumbnailShape::Square->value);
            $table->text('description')->nullable();
            $table->char('country', 2)->nullable();
            $table->boolean('international_shipping')->nullable();
            $table->unsignedBigInteger('etsy_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('photo_id')->references('id')->on('photos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shops');
    }
}
