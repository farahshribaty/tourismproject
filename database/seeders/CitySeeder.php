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
            'Damascus','Aleppo','Latakia', 'Cairo','Alexandria','Ismailia', 'Istanbul','Ankara','Izmir','Dubai','Abu Dhabi','Fujairah',
            'Amman','Irbid','Jerash','Moscow','Saint Petersburg','Sochi','Shanghai','Beijing','Hong Kong','Riyadh','Jeddah','Medina',
            'Paris','Marseille','Nice'
        ];
        $countries = [
            'Syria','Egypt','Turkey','Emirates','Jordan','Russia','China','Saudi Arabia','France'
        ];
//        $Countries = Country::get();
//
//        $idx=0;
//        foreach($Countries as $country){
//            City::create([
//                'name'=>$cities[$idx],
//                'country_id'=>$country['id'],
//            ]);
//            $idx++;
//        }

        for($i=0 ; $i<27 ; $i++){
            City::create([
                'name'=> $cities[$i],
                'country_id'=> (int)($i/3)+1,
            ]);
        }
    }
}
