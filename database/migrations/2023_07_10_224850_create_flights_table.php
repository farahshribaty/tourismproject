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
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_name');
            $table->string('flight_number');
            $table->foreignId('airline_id')->constrained('airlines')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('from')->constrained('countries')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('distination')->constrained('countries')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('available_weight');
            $table->integer('available_seats');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
