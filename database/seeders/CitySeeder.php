<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // adding cities

        $cities = [
            'damascus', 'cairo', 'istanbul'
        ];
        $Countries = Country::get();

        $idx=0;
        foreach($Countries as $country){
            City::create([
                'name'=>$cities[$idx],
                'country_id'=>$country['id'],
            ]);
            $idx++;
        }
    }
}
