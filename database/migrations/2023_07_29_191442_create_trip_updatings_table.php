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
        Schema::create('trip_updatings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_admin_id')->constrained('trip_admins')->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('add_or_update');   // 1 for update, 0 for add.
            $table->boolean('accepted');
            $table->boolean('rejected');
            $table->boolean('seen');

            $table->foreignId('country_id')->nullable();
            $table->foreignId('trip_company_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_updatings');
    }
};
