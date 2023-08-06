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
        Schema::create('hotel_updatings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('location')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('details')->nullable();
            $table->integer('num_of_rooms')->nullable();
            $table->integer('rate')->nullable();
            $table->integer('num_of_ratings')->nullable();
            $table->integer('stars')->nullable();
            $table->integer('price_start_from')->nullable();
            $table->string('website_url')->nullable();
            $table->foreignId('city_id')->constrained('cities')->nullable();
            $table->foreignId('type_id')->constrained('types')->nullable();
            $table->foreignId('admin_id')->constrained('hotel_admins')->nullable();

            $table->foreignId('hotel_admins_id')->constrained('hotel_admins')->nullable();  //->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('hotel_id')->nullable();
            $table->boolean('add_or_update');   // 1 for update, 0 for add.
            $table->boolean('accepted');
            $table->boolean('rejected');
            $table->boolean('seen');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_updatings');
    }
};
