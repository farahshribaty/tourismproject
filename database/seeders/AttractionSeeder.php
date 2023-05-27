<?php

namespace Database\Seeders;

use App\Models\Attraction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Uskudar', 'Restaurant', 'attraction','ahmad','mohamad','firas','hameh','beach','blueTour','helloWorld',
            'dubaiMetro','phobiaDubai','swissotelSpa','naturLifeSpa','dubaiMall','timeLessSpa','helloDubai',
        ];
        $locations = [
            'main Street', 'behind tour','city center'
        ];
        for($i = 0 ; $i<17 ; $i++){

            Attraction::create([
                'city_id'=>random_int(1,3),
                'attraction_type_id'=>random_int(1,7),
                'name'=>$names[$i],
                'email'=>$names[$i].'@email.com',
                'password'=>$names[$i],
                'location'=>$locations[$i%3],
                'phone_number'=> random_int(11111,99999),
                'details'=>$names[$i].' is a beautiful attraction to visit, with its wonderful scenes and perfect service, you will get best experience!',
                'rate'=> random_int(1,5),
                'num_of_ratings'=> random_int(10,3000),
                'adult_price'=>random_int(0,1000),
                'child_price'=>random_int(0,1000),
                'open_at'=>7,
                'close_at'=>23,
                'available_days'=>0111110,
                'website_url'=>'https://attraction.com',
            ]);
        }
    }
}
