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
        Schema::create('used_promo_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pick_up', 255);
            $table->decimal('pick_up_lat', 18, 7);
            $table->decimal('pick_up_long', 18, 7);
            $table->string('destination', 255);
            $table->decimal('destination_lat', 18, 7);
            $table->decimal('destination_long', 18, 7);
            $table->unsignedBigInteger('promo_code_id')->index();
            $table->timestamps();

            $table->foreign('promo_code_id')->references('id')->on('promo_codes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('used_promo_codes');
    }
};
