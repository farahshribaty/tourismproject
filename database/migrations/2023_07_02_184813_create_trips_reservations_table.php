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
        Schema::create('trips_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('date_id');
            $table->foreignId('user_id');
            $table->integer('child');
            $table->integer('adult');
            $table->integer('points_added');
            $table->boolean('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips_reservations');
    }
};
