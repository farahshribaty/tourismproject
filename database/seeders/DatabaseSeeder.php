<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\AttractionReview;
use App\Models\City;
use App\Models\Country;
use App\Models\Facilities;
use App\Models\Hotels_Facilities;
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
        $this->call(CitySeeder::class);
        $this->call(TypeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoomsTypeSeeder::class);
        $this->call(HotelSeeder::class);
        $this->call(AttractionSeeder::class);
        $this->call(AttractionTypeSeeder::class);
        $this->call(FacilitiesSeeder::class);
        $this->call(HotelFacilitiesSeeder::class);
        $this->call(RoomSeeder::class);
        

    }
}
