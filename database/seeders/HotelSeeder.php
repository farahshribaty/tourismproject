<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelAdmin;
use App\Models\HotelPhoto;
use App\Models\HotelReview;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 0 ; $i<17 ; $i++)
        {
            HotelAdmin::create([
                'first_name'=>fake()->name(),
                'last_name'=>fake()->name(),
                'user_name'=>fake()->unique()->name(),
                'email'=>fake()->email(),
                'password'=>fake()->password(),
                'phone_number'=>fake()->phoneNumber(),
            ]);
        }
        
        $names = [
            'Hilton', 'Sheraton', 'Westin',' Four Seasons','Ritz-Carlton','Hyatt','Renaissance','Embassy Suites','blueTour',' InterContinental',
            'Hyatt Regency',' Shangri-La',' Grand Hyatt',' Wyndham',' JW Marriott',' Fairmont','Sofitel',
        ];
        $locations = [
            'main Street', 'behind tour','city center'
        ];
        for($i = 0 ; $i<17 ; $i++){

            Hotel::create([
                'name'=>$names[$i],
                'email'=>$names[$i].'@email.com',
                'location'=>$locations[$i%3],
                'phone_number'=> random_int(11111,99999),
                'details'=>$names[$i].' is a beautiful hotel to stay in, with its wonderful scenes and perfect service, you will get best experience!',
                'num_of_rooms'=>random_int(1,20),
                'rate'=> random_int(1,5),
                'num_of_ratings'=> random_int(10,30),
                'stars'=>random_int(1,5),
                // 'photo' =>'http://127.0.0.1:8000/images/hotel/'.'1685138340.jpg',
                'price_start_from'=>random_int(100,400),
                'website_url'=>'https://hotel.com',
                'city_id'=>random_int(1,9),
                'type_id'=>random_int(1,5),
                'admin_id'=>$i+1
            ]);
        }

        $hotels = Hotel::get();
        $photos = ['1685730895.jpg','1685730895.jpg','1685730895.jpg','1685730895.jpg','1687859851.jpg'];

        foreach($hotels as $hotel){
            for($i=0 ; $i<5 ; $i++){
                HotelPhoto::create([
                    'hotel_id'=> $hotel['id'],
                    'path'=> 'http://127.0.0.1:8000/images/hotel/'.$photos[$i],
                ]);
            }
        }
        for($i = 0 ; $i<17 ; $i++){
            HotelReview::create([
                'user_id'=>random_int(1,20),
                'hotel_id'=>random_int(1,17),
                'rate'=>random_int(1,5),
                'comment'=>'this is my comment'
            ]);
        }

    }
}

