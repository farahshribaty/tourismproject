<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Trip;
use App\Models\TripActivitiesIncluded;
use App\Models\TripActivity;
use App\Models\TripAdmin;
use App\Models\TripCompany;
use App\Models\TripDate;
use App\Models\TripDay;
use App\Models\TripDeparture;
use App\Models\TripFavourite;
use App\Models\TripOffer;
use App\Models\TripPhoto;
use App\Models\TripReview;
use App\Models\TripService;
use App\Models\TripsReservation;
use App\Models\TripUpdating;
use App\Models\User;
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

        // adding admins for trips

        for($i=0 ; $i<7 ; $i++){
            TripAdmin::create([
                'user_name'=> 'tripAdmin'.($i+1).'@gmail.com',
                'password'=> 'admin',
                'full_name'=> 'admin_'.($i+1),
                'phone_number'=> random_int(1111111,9999999),
            ]);
        }

        // adding companies for trips

        $countries = Country::get();
        for($i=0 ; $i<7 ; $i++){
            TripCompany::create([
                'trip_admin_id'=> ($i+1),
                'name'=> 'company'.($i+1),
                'email'=> 'company'.($i+1).'@gmail.com',
                'phone_number'=> 435435,
                'country_id'=> ($i+1)%(sizeof($countries)),
            ]);
        }

        // adding trips
        $trip_names = ['Golden Triangle Tour','Trekking Mont Blanc','Europe Escape','Athens Tour','National Parks Tour',
            'Wonderlands Bali','Isle of Skye','Nile Jewel','Thai Intro','Around Iceland Adventure',
            'Loch Ness','Egypt Explorer','Pharaohs Nile Cruise Adventure','Bali Experience','Europe Taster','North Morocco Adventure',
            'Full Moon Island Hopper','Spirits Of Vietnam ','Spirits Of Vietnam ','Ultimate Europe'];

        $cities = [
            'Damascus','Aleppo','Latakia', 'Cairo','Alexandria','Ismailia', 'Istanbul','Ankara','Izmir','Dubai','Abu Dhabi','Fujairah',
            'Amman','Irbid','Jerash','Moscow','Saint Petersburg','Sochi','Shanghai','Beijing','Hong Kong','Riyadh','Jeddah','Medina',
            'Paris','Marseille','Nice'
        ];

        for($i = 0; $i<20; $i++){
            Trip::create([
                'trip_company_id'=> ($i/3)+1,
                'destination'=> $i+4,
                'description'=> $trip_names[$i],
                'rate'=> random_int(1,5),
                'num_of_ratings'=> random_int(20,1000),
                'details'=> 'Start and end in '.$cities[$i+4].'! With the Explorer tour Ultimate Europe - 26 Days, you have a 26 days tour package taking you through Amsterdam, Netherlands and 26 other destinations in Europe. Ultimate Europe - 26 Days includes accommodation in a hotel as well as an expert guide, meals, transport and more.',
                'days_number'=> random_int(3,15),
                'max_persons'=> random_int(15,50),
                'start_age'=> random_int(1,15),
                'end_age'=> random_int(50,90),
            ]);
        }


        // adding photo for each trip

        $trips = Trip::get();
        $photos = ['0lNgvdaf-2017_Atlantis_base-400x240','5J3EUEHs-2017_Dubai_Frame_base-400x240','ghrdaka',
            '230012_0','247195','france-paris-attractions-parc-de-la-villette-geode-theater','france-paris-eiffel-tower',
            'france-paris-notre-dame','france-paris-place-vendome','france-paris-top-attractions-palais-royal','germany-brandenburg-gate',
            'germany-museum-island-berlin','germany-top-attractions-munichs-marienplatz','Hg6ORRXH-dubaiaquarium_base_1',
            'JfY7BANn-Museum-of-The-Future-2022-400x300','l2ZacHAf-Burj-Al-Arab-Jumeirah-Aerial-at-Sunset-400x300',
            'pexels-haley-black-2087391','pexels-olga-lioncat-7245230','upgJRVIx-Desert-safari-Dubai-400x300','vXztb4PG-2015_1_jumeirahmosque_base_1',];

        $idx = 0;
        foreach($trips as $trip){
            for($i=0 ; $i<5 ; $i++){
                TripPhoto::create([
                    'trip_id'=>$trip['id'],
                    'path'=>'http://127.0.0.1:8000/images/trip/'.$photos[random_int(0,19)].'.jpg',
                ]);
            }
            TripPhoto::create([
                'trip_id'=>$trip['id'],
                'path'=>'http://127.0.0.1:8000/images/trip/'.$photos[$idx++].'.jpg',
            ]);
        }


        // adding some services for each trip

        foreach($trips as $trip){
            for($i=0;$i<4;$i++){
                WhatIsIncluded::create([
                    'trip_id'=>$trip['id'],
                    'service_id'=> $i+1,
                ]);
            }
        }

        // adding some activities for each trip

        foreach($trips as $trip){
            for($i=0;$i<4;$i++){
                TripActivitiesIncluded::create([
                    'trip_id'=>$trip['id'],
                    'activity_id'=>$i+1,
                ]);
            }
        }

        // add details for each day (itinerary)

        $titles = ['visiting museum','swimming','riding bicycle','visiting cinema city'];
        $details = ['Discover the delights of Amsterdam on this free day! Spend the day in the city. Wander through famous museums such as the Van Gogh Museum or the Rijksmuseum. Browse the flower market, relax in one of the parks or explore the city by bicycle - just like the locals do! Or, join the MOCO and House of Bols optional excursion. The MOCO Museum houses modern and contemporary art from the likes of Banksy, Japanese artist Yayoi Kusama and much more! After the museum, head to the House of Bols to learn all about genever (Dutch gin). Choose to complete the free day with the optional Diversely Dutch Dinner!',
            'The tour continues to Germany today. Admire the views as the coach travels through the Dutch and German countryside on the way to Berlin - Germany’s capital city which is full of history and a rejuvenated spirit. Having emerged from a turbulent and dark past, today Berlin bursts with positive energy and features lots to see and experience - including historic sights, intriguing museums, a vibrant cultural scene and exciting nightlife. Look forward to exploring the city’s mix of reminders of life during the Cold War and modern times on the free day tomorrow. Arrive in the city in the late afternoon. Enjoy views of Berlin during a driving tour before dinner this evening, which will be at a lively beer hall.',
            'Look forward to a full free day to explore Berlin! Some of the city’s most famous sites include the Berlin Wall, Brandenburg Gate, Checkpoint Charlie, Alexanderplatz and the Reichstag. Joining the Berlin optional excursion is a great way to discover this vibrant German city with your tour leader - see the East Side Gallery, visit the Museum in the Kulturbrauerei, explore the hidden sites of Hackescher Markt, taste currywurst and view the city from the iconic Berlin TV Tower! Tonight, find a local restaurant in the city to enjoy dinner. Later, make sure to stroll along the city centre streets and to get a taste of the unique atmosphere of Berlin at night!',
            'The fairytale magic of Prague is yours to discover today! With centuries of history, castles, striking buildings and historic bridges, Prague combines old-world wisdom with an essence of youthful energy. Join the Prague optional tour and explore more of the city. Enjoy a sightseeing river cruise including lunch, plus a guided tour of Prague Castle. Alternatively, head out on your own. Visit the clock tower, leave your stamp on the city at the Lennon Wall, see Wenceslas Square, visit the Jewish Quarter or spend time in the Mala Strana district. Sip Czech brew in a beer house and try a traditional trdelnik! Later, enjoy an evening of folk music and dancing on the Traditional Czech Folk Evening optional excursion!'];

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

        // adding dates for each trip (date of trip)

        $trips = Trip::get();

        foreach($trips as $trip){
            for($i=0;$i<3;$i++){
                TripDate::create([
                    'trip_id'=>$trip['id'],
                    'departure_date'=> Carbon::now()->addMonths($i),
                    'current_reserved_people'=> 22,
                    'price'=> ($trip['id']*100),
                ]);
            }
        }

        // adding reservations for each date

        $dates = TripDate::get();

        foreach($dates as $date){
            for($i=0 ; $i<3 ; $i++){
                TripsReservation::create([
                    'date_id'=> $date['id'],
                    'user_id'=> random_int(1,17),
                    'child'=> 2,
                    'adult'=> 2,
                    'points_added'=> 10,
                    'payment'=> 4*$date['price'],
                    'active'=> 1,
                ]);
            }
        }

        // adding some reviews for each trip

        foreach($trips as $trip){
            for($i=0;$i<3;$i++){
                TripReview::create([
                    'user_id'=> ($i+1),
                    'trip_id'=> $trip['id'],
                    'comment'=> ($i==0 ? 'Very good trip! I loved the most the fountains in the city.':'It was a bad trip!'),
                    'stars'=> ($i==0 ? 5:1),
                ]);
            }
        }

        // adding an offer for each trip

        foreach($trips as $trip){
            TripOffer::create([
                'trip_id'=> $trip['id'],
                'percentage_off'=> 20,
                'active'=> 1,
                'offer_end'=> '2023-09-05',
            ]);
        }

        // adding updates for some trips

        $trip_companies = TripCompany::get();
        foreach($trip_companies as $trip_company){
            TripUpdating::create([
                'trip_admin_id'=> $trip_company['trip_admin_id'],
                'trip_company_id'=> $trip_company['id'],
                'add_or_update'=> 1,
                'accepted'=> 0,
                'rejected'=> 0,
                'seen'=> 0,
            ]);
        }

        // adding some reservations:
        foreach($dates as $date){
            TripsReservation::create([
                'date_id'=> $date['id'],
                'user_id'=> random_int(1,17),
                'child'=> 2,
                'adult'=> 2,
                'points_added'=> 20,
                'payment'=> 399,
                'active'=> 1,
            ]);
        }

        // adding some reservations for mohamad user:
        $user = User::where('first_name','=','mohamad')->first();
        foreach($dates as $date){
            TripsReservation::create([
                'date_id'=> $date['id'],
                'user_id'=> $user['id'],
                'child'=> 2,
                'adult'=> 2,
                'points_added'=> 20,
                'payment'=> 399,
                'active'=> 1,
            ]);
        }

        // adding some favourites for mohamad user:
        foreach($trips as $trip){
            TripFavourite::create([
                'user_id'=> $user['id'],
                'trip_id'=> $trip['id'],
            ]);
        }

    }
}
