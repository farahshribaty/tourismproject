<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelAdmin;
use App\Models\HotelFavourite;
use App\Models\HotelPhoto;
use App\Models\HotelReview;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSeederFinal extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // adding admins

        for($i = 0 ; $i<19 ; $i++)
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

        // adding hotels

        $names = [
            'Hilton', 'Sheraton', 'Westin',' Four Seasons','Ritz-Carlton','Hyatt','Renaissance','Embassy Suites','blueTour',' InterContinental',
            'Hyatt Regency',' Shangri-La',' Grand Hyatt',' Wyndham',' JW Marriott',' Fairmont','Sofitel','Damaroze','Qattan'
        ];
        $locations = [
            'main Street', 'behind tour','city center',
        ];

        for($i = 0 ; $i<19 ; $i++){

            Hotel::create([
                'name'=>$names[$i],
                'email'=>$names[$i].'@email.com',
                'location'=>$locations[$i%3],
                'phone_number'=> random_int(11111,99999),
                'details'=>$names[$i].' is a beautiful hotel to stay in, with its wonderful scenes and perfect service, you will get best experience! Mixing smart design with the unexpected, Hotel Hive unites modern innovation with historic character. Our distinctive 125-250 square foot rooms are perfectly unique hives for on-the-go guests looking to truly connect with the city. From its prime location, Hotel Hive guests can buzz about the city and return to a unique and innovative hotel experience.',
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

        // adding photos

        $hotels = Hotel::get();
        $photos = ['governor-s-mansion-montgomery-alabama-grand-staircase-161758','pexels-donald-tong-189296','pexels-photo-137090',
            'pexels-photo-189296','pexels-photo-261102','pexels-photo-594077','pexels-photo-933337','pexels-photo-1001965',
            'pexels-photo-1058759','pexels-photo-1707310','pexels-photo-2034335','pexels-photo-5007455','pexels-photo-5088101',
            'pexels-photo-10436369','pexels-photo-10642591','pexels-photo-11056539','pexels-photo-12690518','pexels-photo-14357627',
            'pexels-pixabay-261102',];

        $idx = 0;
        foreach($hotels as $hotel){
            for($i=0 ; $i<5 ; $i++){
                HotelPhoto::create([
                    'hotel_id'=> $hotel['id'],
                    'path'=> 'http://127.0.0.1:8000/images/hotel/'.$photos[random_int(0,18)].'.jpg',
                ]);
            }
            HotelPhoto::create([
                'hotel_id'=> $hotel['id'],
                'path'=> 'http://127.0.0.1:8000/images/hotel/'.$photos[$idx++].'.jpg',
            ]);
        }

        // adding reviews
        $comments = ['Nice hotel!','I enjoyed myself so much. The room I was in was definitely for one person. It was small (I prefer the term “European”), but had everything I needed. The shower was wonderful, the air conditioning worked so well in the 90+ degree heat, and the bed was very comfy. The people working there (everyone whose names I forgot to get, but especially James and Dana) were great at giving me ideas of where to eat in the neighborhood. Reese was a wonderful bartender, as was Vicki(?). Very fun. The location is great. Walking distance from the Mall, and very near the Foggy Bottom —GWU metro stop which has 3 lines that can take you','Favorite hotel for traveling for business, comfortable, and clean! Great spot, walking and running distance to the monuments and local restaurants. Staff was so friendly, thank you for the great stay!'];

        foreach($hotels as $hotel){
            for($i=0 ; $i<10 ; $i++){
                HotelReview::create([
                    'user_id'=>random_int(1,20),
                    'hotel_id'=> $hotel['id'],
                    'rate'=>random_int(1,5),
                    'comment'=> $comments[$i%3],
                ]);
            }
        }


        // adding some reservations for mohamad user:
        $user = User::where('first_name','=','mohamad')->first();


        // adding some favourites for mohamad user:
        foreach($hotels as $hotel){
            HotelFavourite::create([
                'user_id'=> $user['id'],
                'hotel_id'=> $hotel['id'],
            ]);
        }


    }
}
