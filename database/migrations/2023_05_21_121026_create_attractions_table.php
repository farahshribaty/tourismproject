<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id');
            $table->foreignId('attraction_type_id');
            $table->foreignId('attraction_admin_id');
            $table->string('name');
            $table->string('email')->unique();
//            $table->string('password');
            $table->string('location');
            $table->string('phone_number');
            $table->integer('rate')->nullable();
            $table->integer( 'num_of_ratings')->nullable();
            $table->dateTime('open_at');
            $table->dateTime('close_at');
            $table->integer('available_days');
            $table->integer('child_ability_per_day');
            $table->integer('adult_ability_per_day');
            $table->text('details');
            $table->string('website_url')->nullable();
            $table->integer('adult_price')->nullable();  //in USD
            $table->integer('child_price')->nullable();
            $table->integer('points_added_when_booking');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attractions');
    }
};
