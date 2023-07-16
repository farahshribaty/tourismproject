<?php

namespace Database\Seeders;

use App\Models\Flights;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FlightsSeeder extends Seeder
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

        $class = [
            'First Class','Second Class','Bissness Class'
        ];

        for($i = 0 ; $i<17 ; $i++)
        {
            Flights::create([
                'flight_name'=>$names[$i],
                'flight_number'=>random_int(1000,20000),
                'airline_id'=>random_int(1,17),
                'from'=> random_int(1,3),
                'distination'=>random_int(1,3),
                'carry_on_bag'=> random_int(1,5),
                'checked_bag'=> random_int(1,5),
                'duration'=>Carbon::now()->addHours(random_int(1, 12))->format('H:i:s'),
                'departure_time'=>Carbon::now()->addDays(random_int(1, 30))->setTime(random_int(0, 23),
                 random_int(0, 59), random_int(0, 59)),
                'arrival_time'=>Carbon::now()->addDays(random_int(1, 30))->setTime(random_int(0, 23),
                 random_int(0, 59), random_int(0, 59)),
                'available_seats'=>random_int(50,100),
                'flight_class'=>$class[$i%3]
            ]);
        }
    }
}
