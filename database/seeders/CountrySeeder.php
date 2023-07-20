<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // adding countries

        $countries = [
            'Syria','Egypt','Turkey','Emirates','Jordan','Eraq','Moscuo','China','Saudi Arabia'
        ];
        foreach($countries as $country){
            Country::create([
                'name'=>$country,
            ]);
        }
    }
}
