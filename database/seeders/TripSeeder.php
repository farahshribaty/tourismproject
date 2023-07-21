<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\TripActivitiesIncluded;
use App\Models\TripActivity;
use App\Models\TripDate;
use App\Models\TripDay;
use App\Models\TripDeparture;
use App\Models\TripOffer;
use App\Models\TripPhoto;
use App\Models\TripReview;
use App\Models\TripService;
use App\Models\WhatIsIncluded;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // adding services of trips

        $services = ['Accommodation','Guide','COVID-19 Health & Safety Measures','Meals','Transport','Flights','Insurance'];
        foreach($services as $service){
            TripService::create([
                'service'=> $service,
            ]);
        }

        // adding activities for trips

        $activities = ['Exploring','Bicycle','River Cruise','Festival & Events','In-depth Cultural'];
        foreach($activities as $activity){
            TripActivity::create([
                'activity'=>$activity,
            ]);
        }

        // adding trips

        for($i = 0; $i<17; $i++){
            Trip::create([
                'trip_company_id'=> $i%6+1,
                'destination'=> ($i%3)+1,
                'description'=>'river cruise',
                'rate'=> random_int(1,5),
                'num_of_ratings'=> random_int(20,1000),
                'details'=> 'Start and end in Strasbourg! With the River Cruise tour A Bountiful Christmas in Alsace and the Black Forest.',
                'days_number'=> random_int(3,15),
                'max_persons'=> random_int(15,50),
                'start_age'=> random_int(1,15),
                'end_age'=> random_int(50,90),
            ]);
        }


        // adding photo for each trip

        $trips = Trip::get();

        foreach($trips as $trip){
            TripPhoto::create([
                'trip_id'=>$trip['id'],
                'path'=>'http://127.0.0.1:8000/images/trip/'.'1234567.jpg',
            ]);
        }

        // adding offers for the first 5 trips
        $cnt=0;
        foreach($trips as $trip){
            if($cnt == 5) break;

            TripOffer::create([
                'trip_id'=> $trip['id'],
                'percentage_off'=> (($cnt+1)*10),
                'active'=> true,
                'offer_end'=> Carbon::now()->addDays(3),
            ]);

            $cnt++;
        }

        // adding departures for each trip

//        foreach($trips as $trip){
//            for($i=0;$i<3;$i++){
//                TripDeparture::create([
//                    'trip_id'=>$trip['id'],
//                    'flight_id'=>0,
//                    'city_id'=>$i+1,
//                    'departure_details'=>($i==$trip['destination'] ? 'You are already in the target city! there is no further fees!':'The trip will start from this city with the following flight')
//                ]);
//            }
//        }

        // adding some services for each trip

        foreach($trips as $trip){
            for($i=0;$i<=4;$i++){
                WhatIsIncluded::create([
                    'trip_id'=>$trip['id'],
                    'service_id'=>$i,
                ]);
            }
        }

        // adding some activities for each trip

        foreach($trips as $trip){
            for($i=0;$i<=4;$i++){
                TripActivitiesIncluded::create([
                    'trip_id'=>$trip['id'],
                    'activity_id'=>$i+1,
                ]);
            }
        }

        // add details for each day (itinerary)

        $titles = ['visiting museum','swimming','riding bicycle','visiting cinema city'];
        $details = ['we will visit the historical museum in the city center, there are lots of things you need to watch',
            'the next day, we will go to the beach, where sun hitting the golden sand!',
            'in this day, it is valuable to go around the city in a tour using bicycle',
            'a new film produced locally will be showed in the cinema city'];

        foreach($trips as $trip){
            for($i=0;$i<$trip['days_number'];$i++){
                TripDay::create([
                    'trip_id'=> $trip['id'],
                    'day_number'=> ($i+1),
                    'title'=> ($i==0 ? 'arrival':$titles[$i%4]),
                    'details'=> ($i==0 ? 'this is the first day here! just rest in this day and entertainment will start from the next day.':$details[$i%4]),
                ]);
            }
        }

        // adding dates for each departure (date of trip)

        $trips = Trip::get();

        foreach($trips as $trip){
            for($i=0;$i<3;$i++){
                TripDate::create([
                    'trip_id'=>$trip['id'],
                    'departure_date'=> Carbon::now()->addMonths($i),
                    'price'=> ($trip['id']*100),
                ]);
            }
        }

        // adding some reviews for each trip

        foreach($trips as $trip){
            for($i=0;$i<3;$i++){
                TripReview::create([
                    'user_id'=> ($i%2)+1,
                    'trip_id'=> $trip['id'],
                    'comment'=> ($i==0 ? 'Very good trip! I loved the most the fountains in the city.':'what a fucking trip!'),
                    'stars'=> ($i==0 ? 5:1),
                ]);
            }
        }


    }
}
