<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\AttractionReview;
use App\Models\City;
use App\Models\Country;
use App\Models\Facilities;
use App\Models\HotelsFacilities;
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

        // country and city
        $this->call(CountrySeeder::class);
        $this->call(CitySeeder::class);

        // user
        $this->call(UserSeeder::class);

        // hotels
        $this->call(TypeSeeder::class);
        $this->call(FacilitiesSeeder::class);
        $this->call(HotelSeederFinal::class);
        $this->call(HotelFacilitiesSeeder::class);
        $this->call(RoomsTypeSeeder::class);
        $this->call(RoomSeederFinal::class);

        // attractions
        $this->call(AttractionSeeder::class);

        // trips
//        $this->call(TripCompanySeeder::class);
        $this->call(TripSeeder::class);

        //airline
        $this->call(AirLineSeeder::class);

        //flights
        $this->call(FlightsSeederFinal::class);


    }
}
