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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
//            $table->boolean('Wifi');
//            $table->boolean('Parking');
//            $table->boolean('Transportation');
//            $table->boolean('Formalization');
//            $table->string('activities');
//            $table->string('meals');
           // $table->enum('Meals',['Breakfast,Lunch,Dinner,Other']);
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
