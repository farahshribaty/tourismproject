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
        Schema::create('attraction_admins', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('attraction_id')->constrained('attractions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('user_name')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->integer('phone_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attraction_admins');
    }
};
