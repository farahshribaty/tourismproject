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
            'Syria','Egypt','Turkey','Emirates','Jordan','Russia','China','Saudi Arabia','France'
        ];
        $photos = [
            '1548776-1904225677', 'cairo','1548776-1904225677','dubai-skyline','petra-6294051_1280','Moscow_International_Business_Center7','Rumeli-Fortress',
            '1464744716','france-paris-top-tourist-attractions-musee-du-louvre',
        ];
//        foreach($countries as $country){
//            Country::create([
//                'name'=>$country,
//                'path'=>'http://127.0.0.1:8000/images/countries/'.'1111.jpg',
//            ]);
//        }

        for($i=0 ; $i<9 ; $i++){
            Country::create([
                'name'=> $countries[$i],
                'path'=> $photos[$i],
            ]);
        }


    }
}
