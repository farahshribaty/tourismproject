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
        Schema::create('hotels_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels');
            $table->foreignId('facilities_id')->constrained('facilities');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels_facilities');
    }
};
