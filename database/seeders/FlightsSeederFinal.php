<?php

namespace Database\Seeders;

use App\Models\FlightFavourites;
use App\Models\Flights;
use App\Models\FlightsReservation;
use App\Models\FlightsTime;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FlightsSeederFinal extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Emirates Airline', 'Qatar Airways', 'Saudi Arabian Airlines','China Airlines','Turkish Airlines'
            ,'American Airlines','WestJet','Air France KLM','Aeroflot Russian Airlines'
            ,'Alitalia','Asiana Airline','British Airways','JAL Group','NatureLifeSpa'
            ,'Air Canada','United Airlines','SkyWest Airlines',
        ];

        $class = [
            'First Class','Second Class','Business Class'
        ];

        for($i = 0 ; $i<40 ; $i++)
        {
            Flights::create([
                'flight_name'=>$names[$i%3],
                'flight_number'=>random_int(1000,20000),
                'airline_id'=>random_int(1,17),
                'from'=> random_int(1,9),
                'distination'=>random_int(1,9),
                'available_weight'=> random_int(50,70),
                'available_seats'=>random_int(50,100),
            ]);
        }

        // for($i = 0 ; $i<17 ; $i++)
        // {
        //     Flights::create([
        //         'flight_name'=>$names[$i],
        //         'flight_number'=>random_int(1000,20000),
        //         'airline_id'=>random_int(1,17),
        //         'from'=> ($i%2==0 ? 1:2),
        //         'distination'=> ($i%2==0 ? 2:1),
        //         'available_weight'=> random_int(50,70),
        //         'available_seats'=>random_int(50,100)
        //     ]);
        // }

        // adding dates for all flights:

        $flights = Flights::get();

        foreach($flights as $flight){
            for($i=0 ; $i<=10 ; $i++){
                $fromHour = Carbon::now()->addHours(random_int(1, 6));
                $toHour = Carbon::now()->addHours(random_int(6, 12));

                $durationInMinutes = $toHour->diffInMinutes($fromHour);
                $duration = Carbon::now()->startOfDay()->addMinutes($durationInMinutes)->format('H:i:s');


                FlightsTime::create([
                    'departe_day'=>Carbon::now()->addDays(random_int(1, 30)),
                    'From_hour' => $fromHour->format('H:i:s'),
                    'To_hour' => $toHour->format('H:i:s'),
                    'duration'=>  $duration,
                    'flights_id'=>$flight['id'],
                    'adults_price'=>random_int(1000,2000),
                    'children_price'=>random_int(100,400),
                ]);
            }
        }

        // for($i = 0 ; $i<17 ; $i++){

        //     $fromHour = Carbon::now()->addHours(random_int(1, 6));
        //     $toHour = Carbon::now()->addHours(random_int(6, 12));

        //     $durationInMinutes = $toHour->diffInMinutes($fromHour);
        //     $duration = Carbon::now()->startOfDay()->addMinutes($durationInMinutes)->format('H:i:s');


        //     FlightsTime::create([
        //         'departe_day'=>($i%2==0 ? '2023-9-10':'2023-9-15'),
        //         'From_hour' => $fromHour->format('H:i:s'),
        //         'To_hour' => $toHour->format('H:i:s'),
        //         'duration'=>  $duration,
        //         'flights_id'=>random_int(1,10),
        //         'adults_price'=>random_int(1000,2000),
        //         'children_price'=>random_int(100,400),
        //     ]);
        // }

        // adding reservations

        for($i = 0 ; $i<30 ; $i++){
            FlightsReservation::create([
                'user_id'=>random_int(1,17),
                'flights_times_id'=>random_int(1,30),
                'num_of_adults'=>random_int(1,10),
                'num_of_children'=>random_int(1,10),
                'payment'=>random_int(100,1000),
                'Points'=>random_int(1,10)
            ]);
        }

        $flights = Flights::get();
        // adding some reservations for mohamad user:
        $user = User::where('first_name','=','mohamad')->first();
        foreach($flights as $flight){
            FlightsReservation::create([
                'user_id'=> $user['id'],
                'flights_times_id'=>random_int(1,30),
//                'flight_class'=>$class[$i%3],
                'num_of_adults'=>random_int(1,10),
                'num_of_children'=>random_int(1,10),
                'payment'=>random_int(100,1000),
                'Points'=>random_int(1,10)
            ]);
        }

        // adding some favourites for mohamad user:
        foreach($flights as $flight){
            FlightFavourites::create([
                'user_id'=> $user['id'],
                'flight_id'=> $flight['id'],
            ]);
        }
    }
}
