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
        Schema::create('trip_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_company_id')->constrained('trip_companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('user_name')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_admins');
    }
};
