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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('location');
            $table->string('phone_number');
            $table->text('details');
            $table->integer('num_of_rooms');
            $table->integer('rate')->nullable();
            $table->integer('num_of_ratings')->nullable();
            $table->integer('stars');
            $table->integer('price_start_from');
            $table->string('website_url')->nullable();
            $table->foreignId('city_id')->constrained('cities');
            $table->foreignId('type_id')->constrained('types');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
