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
        Schema::create('trip_travelers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->dateTime('birth');
            $table->boolean('gender');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_travelers');
    }
};
