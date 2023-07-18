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
            $table->foreignId('airline_id')->constrained('airlines');
            $table->foreignId('from')->constrained('countries');
            $table->foreignId('distination')->constrained('countries');
            $table->integer('carry_on_bag');
            $table->integer('checked_bag');
            $table->time('duration');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->integer('available_seats');
            $table->string('flight_class');
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
