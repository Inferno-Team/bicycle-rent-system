<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bicycles', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("lat")->nullable();
            $table->string("long")->nullable();
            $table->string("img_url")->nullable();
            $table->boolean("is_available")->default(true);
            $table->double("price_per_time");
            $table->double("price_per_distance");
            $table->boolean("is_sport")->default(false);
            $table->foreignId('style_id')->references('id')->on('bicycle_styles');
            $table->foreignId('stand_id')->references('id')->on('stands');
            $table->foreignId('esp32_id')->references('id')->on('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bicycles');
    }
};
