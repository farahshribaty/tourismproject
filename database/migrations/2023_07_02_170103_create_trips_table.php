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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_company_id')->constrained('trip_companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('destination');
            $table->string('description');
            $table->text('details');
            $table->integer('days_number');
            $table->integer('max_persons');
            $table->float('rate');
            $table->integer('num_of_ratings');
            $table->integer('start_age');
            $table->integer('end_age');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
