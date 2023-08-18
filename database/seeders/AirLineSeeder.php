<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Airline;
use App\Models\AirlineAdmin;
use Illuminate\Validation\Rules\Unique;

class AirLineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // adding admins:

        for($i = 0 ; $i<30 ; $i++)
        {
            AirlineAdmin::create([
                'first_name'=>fake()->name(),
                'last_name'=>fake()->name(),
                'user_name'=>fake()->unique()->name(),
                'email'=>fake()->email(),
                'password'=>fake()->password(),
                'phone_number'=>fake()->phoneNumber(),
            ]);
        }

        $names = [
            'Emirates Airline', 'Qatar Airways', 'Saudi Arabian Airlines','China Airlines','Turkish Airlines'
            ,'American Airlines','WestJet','Air France KLM','Aeroflot Russian Airlines'
            ,'Alitalia','Asiana Airline','British Airways','JAL Group','NaturLifeSpa'
            ,'Air Canada','United Airlines','SkyWest Airlines',
        ];
        $locations = [
            'Dubai', 'Doha','Jeddah','Taipei','Istanbul','Dallas
            Fort Worth','Montreal','Quebec','Calgary'
        ];

        $photos = ['ABX-Air-logo-tumb.jpg','AccessAir-Logo-tumb.png','ACES-Colombia-Logo-thumb.png','AeroBratsk-Logo-tumb.png','Air-Bagan-Logo-tumb.png',
            'Air-Bourbon-Logo-tumb.png','Air-China-Logo-tumb.jpg','Air-Dolomiti-Logo-thumb.png'];

        for($i = 0 ; $i<20 ; $i++)
        {
            Airline::create([
                'name'=>$names[$i%2],
                'email'=>$names[$i%16].$i.'@email.com',
                'location'=>$locations[$i%9],
                'phone_number'=> random_int(11111,99999),
                'details'=>$names[$i%16].': Hope enjoy your flight with us',
                'rate'=> random_int(1,5),
                'num_of_ratings'=> random_int(10,3000),
                'path'=>'http://127.0.0.1:8000/images/airline/'.$photos[$i%8],
                'country_id'=>random_int(1,9),
                'admin_id'=>$i+1
            ]);
        }


    }
}
