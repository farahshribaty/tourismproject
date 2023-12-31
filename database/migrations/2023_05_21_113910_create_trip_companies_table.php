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
        Schema::create('trip_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_admin_id')->constrained('trip_admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('country_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_companies');
    }
};
