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
        Schema::create('attraction_updatings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attraction_admin_id');
            $table->foreignId('attraction_id')->nullable();
            $table->boolean('add_or_update');   // 1 for update, 0 for add.
            $table->boolean('accepted');
            $table->boolean('rejected');
            $table->boolean('seen');

            $table->foreignId('city_id')->nullable();
            $table->foreignId('attraction_type_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
//            $table->string('password')->nullable();
            $table->string('location')->nullable();
            $table->string('phone_number')->nullable();
//            $table->integer('rate')->nullable();
//            $table->integer( 'num_of_ratings')->nullable();
            $table->dateTime('open_at')->nullable();
            $table->dateTime('close_at')->nullable();
            $table->integer('available_days')->nullable();
            $table->integer('child_ability_per_day')->nullable();
            $table->integer('adult_ability_per_day')->nullable();
            $table->text('details')->nullable();
            $table->string('website_url')->nullable();
            $table->integer('adult_price')->nullable();  //in USD
            $table->integer('child_price')->nullable();
            $table->integer('points_added_when_booking')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attraction_updatings');
    }
};
