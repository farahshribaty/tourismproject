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
        Schema::create('flights_times', function (Blueprint $table) {
            $table->id();
            $table->date('departe_day');
            $table->time('From_hour');
            $table->time('To_hour');
            $table->time('duration');
            $table->foreignId('flights_id')->constrained('flights');
            $table->integer('adults_price');
            $table->integer('children_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights_times');
    }
};
