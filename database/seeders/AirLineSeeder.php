<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Airline;

class AirLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Emirates Airline', 'Qatar Airways', 'Saudi Arabian Airlines','China Airlines','Turkish Airlines'
            ,'American Airlines','WestJet','Air France KLM','Aeroflot Russian Airlines'
            ,'Alitalia','Asiana Airline','British Airways','JAL Group','NaturLifeSpa'
            ,'Air Canada','United Airlines','SkyWest Airlines',
        ];
        $locations = [
            'Dubai', 'Doha','Jeddah','Taipei','Istanbul','Dallas
            Fort Worth','Montreal, Quebec','Calgary'
        ];

        for($i = 0 ; $i<17 ; $i++)
        {
            Airline::create([
                'name'=>$names[$i],
                'email'=>$names[$i].'@email.com',
                'location'=>$locations[$i%3],
                'phone_number'=> random_int(11111,99999),
                'details'=>$names[$i].': Hope enjoy your flight with us',
                'rate'=> random_int(1,5),
                'num_of_ratings'=> random_int(10,3000),
                'country_id'=>random_int(1,3)
            ]);
        }


    }
}
