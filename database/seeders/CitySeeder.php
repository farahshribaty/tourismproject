<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::create([
            'name'=>'damascus',
            'country_id'=>1,
        ]);
        City::create([
            'name'=>'cairo',
            'country_id'=>2,
        ]);
        City::create([
            'name'=>'istanbul',
            'country_id'=>3,
        ]);
    }
}
