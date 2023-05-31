<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Types;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\City::factory(10)->create();
        // \App\Models\Country::factory(10)->create();

        $this->call(CountrySeeder::class);
        //$this->call(CitySeeder::class);
        $this->call(TypeSeeder::class);

    }
}
