<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\AttractionAdmin;
use App\Models\AttractionPhoto;
use App\Models\AttractionReview;
use App\Models\AttractionType;
use App\Models\AttractionUpdating;
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

        for($i=0 ; $i<17 ; $i++){
            AttractionAdmin::create([
                'user_name'=> 'attractionAdmin'.($i+1).'@gmail.com',
                'password'=> 'admin',
                'full_name'=> 'admin_'.($i+1),
                'phone_number'=> random_int(1111111,9999999),
            ]);
        }


        // adding attractions

        $names = [
            'Uskudar', 'Restaurant', 'UmayyadMosque','UpTown','Bloudan','KhalifaTower','Bukain','BlueBeach','blueTour','HelloWorld',
            'DubaiMetro','PhobiaDubai','SwissotelSpa','NaturLifeSpa','DubaiMall','TimeLessSpa','GelloDubai',
        ];
        $locations = [
            'main Street', 'behind tour','city center'
        ];
        for($i = 0 ; $i<17 ; $i++){

            Attraction::create([
                'city_id'=>random_int(1,3),
                'attraction_type_id'=>random_int(1,7),
                'attraction_admin_id'=> ($i+1),
                'name'=>$names[$i],
                'email'=>$names[$i].'@email.com',
//                'password'=>$names[$i],
                'location'=>$locations[$i%3],
                'phone_number'=> random_int(11111,99999),
                'details'=>$names[$i].' is a beautiful attraction to visit, with its wonderful scenes and perfect service, you will get best experience!',
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

        for($i=0 ; $i<10 ; $i++){
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
        $photos = ['1685138340.jpg','posx.jpg','1690407857.wallpaper.jpg','1690408944.jpg','1690409198.jpg'];

        foreach($attrs as $attr){
            for($i=0 ; $i<5 ; $i++){
                AttractionPhoto::create([
                    'attraction_id'=> $attr['id'],
                    'path'=> 'http://127.0.0.1:8000/images/attraction/'.$photos[$i],
                ]);
            }
        }

        // adding some reviews
        foreach($attrs as $attr){
            for($i=0;$i<3;$i++){
                AttractionReview::create([
                    'user_id'=> ($i+1),
                    'attraction_id'=> $attr['id'],
                    'comment'=> ($i==0 ? 'Very good trip! I loved the most the fountains in the city.':'what a fucking trip!'),
                    'stars'=> ($i==0 ? 5:1),
                ]);
            }
        }
    }
}
