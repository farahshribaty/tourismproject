<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\AttractionAdmin;
use App\Models\AttractionFavourite;
use App\Models\AttractionPhoto;
use App\Models\AttractionReservation;
use App\Models\AttractionReview;
use App\Models\AttractionType;
use App\Models\AttractionUpdating;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // adding attraction types

        $types = [
            'Natural attraction',
            'Cultural attraction',
            'Entertainment attraction',
            'Sports Attraction',
            'Urban Attraction',
            'Religious Attraction',
            'Adventure Attraction',
            'Eco_tourism',
        ];

        $details = [
            'These are attractions that are naturally occurring in the environment, such as mountains, beaches, waterfalls, and national parks.',
            'These are attractions that are related to the culture and history of a place, such as museums, historical sites, and monuments.',
            'These are attractions that are primarily designed for entertainment purposes, such as amusement parks, zoos, and aquariums.',
            'Entertainment attractions: These are attractions that are primarily designed for entertainment purposes, such as amusement parks, zoos, and aquariums.',
            'These are attractions that are related to sports and physical activities, such as ski resorts, golf courses, and sports stadiums.',
            'These are attractions that are related to religion and spirituality, such as temples, churches, and pilgrimage sites.',
            'These are attractions that offer adventure activities such as bungee jumping, zip-lining, and rock climbing.',
            'These are attractions that are designed to promote sustainable tourism and preserve natural environments, such as wildlife reserves and eco-lodges.',
        ];

        // adding types

        for($i=0 ; $i<8 ; $i++){
            AttractionType::create([
                'type'=>$types[$i],
                'details'=>$details[$i],
            ]);
        }

        // adding admins

        for($i=0 ; $i<23 ; $i++){
            AttractionAdmin::create([
                'user_name'=> 'attractionAdmin'.($i+1).'@gmail.com',
                'password'=> 'admin',
                'full_name'=> 'admin_'.($i+1),
                'phone_number'=> random_int(1111111,9999999),
            ]);
        }


        // adding attractions

        $names = [
            'Atlantis Base','Attractions_seine_river','Chatelet','Eiffel','Luxembourg Gardens','Notre Dame','Opera House','Orsay Museum',
            'Pantheon','Place De La Concorde','Place Des Vosges','Place Vendome','Sacre Coeur','Sainte Chapelle','Arc De Triomphe',
            'La Conciergerie','Palais Royal','Musee Du Louvre','Island Berlin','DubaiAquarium Base','Burj Al Arab','Parc de la villette',
            'Jumeirah mosque'
        ];
        $locations = [
            'main Street', 'behind tour','city center'
        ];

        for($i = 0 ; $i<23 ; $i++){
            Attraction::create([
                'city_id'=>random_int(1,3),
                'attraction_type_id'=>random_int(1,7),
                'attraction_admin_id'=> ($i%17+1),
                'name'=>$names[$i%17],
                'email'=>$names[$i%17].$i.'@email.com',
//                'password'=>$names[$i],
                'location'=>$locations[$i%3],
                'phone_number'=> random_int(11111,99999),
                'details'=>$names[$i%17].' is a beautiful attraction to visit, with its wonderful scenes and perfect service, you will get best experience!',
                'rate'=> random_int(1,5),
                'num_of_ratings'=> random_int(10,3000),
                'adult_price'=>random_int(0,1000),
                'child_price'=>random_int(0,1000),
                'open_at'=>'2023-03-02 01:30:00',
                'close_at'=>'2023-03-02 09:30:00',
                //'photo'=>'http://127.0.0.1:8000/images/attraction/'.'1685138340.jpg',
                'available_days'=>62,
                'website_url'=>'https://attraction.com',
                'child_ability_per_day'=>5,
                'adult_ability_per_day'=>5,
                'points_added_when_booking'=>3,
            ]);
        }

        // adding some updates

        for($i=0 ; $i<20 ; $i++){
            AttractionUpdating::create([
                'attraction_id'=> ($i+1),
                'attraction_admin_id'=> ($i+1),
                'add_or_update'=> 1,
                'accepted'=> 0,
                'rejected'=> 0,
                'seen'=> 0,
                'name'=> 'firas zalameh mfakas'
            ]);
        }

        //adding photo for each attraction

        $attrs = Attraction::get();
        $photos = [
            'Atlantis_base','france-paris-attractions-seine-river-cruise-at-sunset','france-paris-chatelet','france-paris-eiffel-tower',
            'france-paris-luxembourg-gardens','france-paris-notre-dame','france-paris-opera-house','france-paris-orsay-museum',
            'france-paris-pantheon','france-paris-place-de-la-concorde-١','france-paris-place-des-vosges','france-paris-place-vendome',
            'france-paris-sacre-coeur','france-paris-sainte-chapelle-2','france-paris-top-attractions-arc-de-triomphe',
            'france-paris-top-attractions-la-conciergerie','france-paris-top-attractions-palais-royal',
            'france-paris-top-tourist-attractions-musee-du-louvre','germany-museum-island-berlin','Hg6ORRXH-dubaiaquarium_base_1',
            'l2ZacHAf-Burj-Al-Arab-Jumeirah-Aerial-at-Sunset-400x300','parc_de_la_villette','vXztb4PG-2015_1_jumeirahmosque_base_1'
        ];

        $idx = 0;
        foreach($attrs as $attr){
            for($i=0 ; $i<5 ; $i++){
                AttractionPhoto::create([
                    'attraction_id'=> $attr['id'],
                    'path'=> 'http://127.0.0.1:8000/images/attraction/'.$photos[random_int(0,22)].'.jpg',
                ]);
            }
            AttractionPhoto::create([
                'attraction_id'=> $attr['id'],
                'path'=> 'http://127.0.0.1:8000/images/attraction/'.$photos[$idx++].'.jpg',
            ]);
        }

        // adding some reviews
        foreach($attrs as $attr){
            for($i=0;$i<10;$i++){
                AttractionReview::create([
                    'user_id'=> random_int(1,10),
                    'attraction_id'=> $attr['id'],
                    'comment'=> ($i%2==0 ? 'Very good trip! I loved the most the fountains in the city.':'It was a bad trip!'),
                    'stars'=> ($i%2==0 ? 5:1),
                ]);
            }
        }

        // adding some reservations for mohamad user:
        $user = User::where('first_name','=','mohamad')->first();
        foreach($attrs as $attr){
            AttractionReservation::create([
                'user_id'=> $user['id'],
                'first_name'=> 'mohamad',
                'last_name'=> 'qattan',
                'attraction_id'=> $attr['id'],
                'book_date'=> '2023-11-11',
                'adults'=> 1,
                'children'=> 1,
                'payment'=> 230,
                'points_added'=> 19,
            ]);
        }


        // adding some reservations:
        foreach($attrs as $attr){
            for($i=0 ; $i<=10 ; $i++){
                AttractionReservation::create([
                    'user_id'=> $i+1,
                    'first_name'=> 'mohamad',
                    'last_name'=> 'qattan',
                    'attraction_id'=> $attr['id'],
                    'book_date'=> now()->adddays(random_int(1,10)),
                    'adults'=> 1,
                    'children'=> 1,
                    'payment'=> random_int(100,1000),
                    'points_added'=> random_int(10,20),
                ]);
            }
        }

        // adding some favourites for mohamad user:
        foreach($attrs as $attr){
            AttractionFavourite::create([
                'user_id'=> $user['id'],
                'attraction_id'=> $attr['id'],
            ]);
        }

        // adding some favourites:
        foreach($attrs as $attr){
            for($i=0 ; $i<10 ; $i++){
                AttractionFavourite::create([
                    'user_id'=> $i+1,
                    'attraction_id'=> $attr['id'],
                ]);
            }
        }

    }
}
