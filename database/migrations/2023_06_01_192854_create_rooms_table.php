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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type')->constrained('room_types'); //->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained('hotels'); //->onDelete('cascade');
            $table->string('details');
            $table->integer('Price_for_night');
            $table->integer('rate')->nullable();
            $table->integer('num_of_ratings')->nullable();
            $table->integer('Sleeps');
            $table->integer('Beds');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
