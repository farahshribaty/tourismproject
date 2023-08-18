<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelReservation;
use App\Models\Room;
use App\Models\RoomFeatures;
use App\Models\RoomPhotos;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RoomSeederFinal extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bedsPerType = [
            1 => 1, // Room type ID 1 has 1 bed
            2 => 2, // Room type ID 2 has 2 beds
            3 => 2, // Room type ID 3 has 2 beds
            4 => 3, // Room type ID 4 has 3 beds
            5 => 3, // Room type ID 5 has 3 beds
            6 => 4, // Room type ID 6 has 4 beds
            7 => 4, // Room type ID 7 has 4 beds
            8 => 5, // Room type ID 8 has 5 beds
            9 => 5, // Room type ID 9 has 5 beds
        ];

        $roomType = random_int(1, 9);
        $numBeds = $bedsPerType[$roomType];

        // adding some 10 rooms for each hotel:

        $hotels = Hotel::get();
        foreach($hotels as $hotel){
            for($i=0 ; $i<10 ; $i++){
                Room::create([
                    'room_type'=>random_int(1,9),
                    'hotel_id'=>$hotel['id'],
                    'details'=>'Mixing smart design with the unexpected, Hotel Hive unites modern innovation with historic character. Our distinctive 125-250 square foot rooms are perfectly unique hives for on-the-go guests looking to truly connect with the city.',
                    'price_for_night'=>random_int(100,500),
                    'rate'=>random_int(1,5),
                    'num_of_ratings'=> random_int(10,3000),
                    'Sleeps'=>random_int(1,5),
                    'Beds'=>$numBeds
                ]);
            }
        }

        // adding features for each room:

        $rooms = Room::get();
        foreach($rooms as $room){
            for($i=1 ; $i<=5 ; $i++){
                RoomFeatures::create([
                    'room_id'=> $room['id'],
                    'features_id'=> $i,
                ]);
            }
        }

        // adding photos for each room:

        $photos = ['pexels-burst-545034','pexels-curtis-adams-3935331','pexels-curtis-adams-4119831','pexels-curtis-adams-6035359',
            'pexels-dmitry-zvolskiy-2062431','pexels-dmitry-zvolskiy-2062431-١','pexels-files-1648776','pexels-jean-van-der-meulen-1454806',
            'pexels-jean-van-der-meulen-1454806-١','pexels-jean-van-der-meulen-1543439','pexels-lisa-fotios-1444424',
            'pexels-max-rahubovskiy-8082562','pexels-max-rahubovskiy-8135105','pexels-pixabay-164595','pexels-pixabay-261102',
            'pexels-pixabay-261169','pexels-pixabay-271618','pexels-pixabay-271619','pexels-pixabay-271624','pexels-pixabay-271639',
            'pexels-pixabay-271815','pexels-pixabay-276688','pexels-pixabay-276688-١','pexels-pixabay-279746','pexels-rajesh-kumar-verma-8236023',
            'pexels-suhel-vba-3659683','pexels-thorsten-technoman-338504','pexels-vecislavas-popa-1643383','pexels-vecislavas-popa-1743229',];


        foreach($rooms as $room){
            for($i=0 ; $i<29 ; $i++){
                RoomPhotos::create([
                    'room_id'=>$room['id'],
                    'path'=>'http://127.0.0.1:8000/images/room/'.$photos[$i].'.jpg',
                ]);
            }
        }

        // adding some reservations:

        for($i = 0 ; $i<40; $i++){
            HotelReservation::create([
                'user_id'=>random_int(1,15),
                'first_name'=>fake()->name(),
                'last_name'=>fake()->name(),
                'hotel_id'=>random_int(1,17),
                'room_id'=>random_int(1,60),
                'check_in'=>Carbon::now()->addDays(random_int(1, 14))->setTime(random_int(0, 23),
                    random_int(0, 59), random_int(0, 59)),
                'check_out'=>Carbon::now()->addDays(random_int(15, 30))->setTime(random_int(0, 23),
                    random_int(0, 59), random_int(0, 59)),
                'num_of_adults'=>random_int(1,20),
                'num_of_children'=>random_int(1,10),
                'payment'=>random_int(1,10)
            ]);
        }

        // adding some reservations for mohamad user:

        $user = User::where('first_name','=','mohamad')->first();
        foreach($rooms as $room){
            HotelReservation::create([
                'user_id'=> $user['id'],
                'first_name'=> 'mohamad',
                'last_name'=> 'qattan',
                'hotel_id'=> $room['hotel_id'],
                'room_id'=> $room['id'],
                'check_in'=> '2023-11-11',
                'check_out'=> '2023-11-12',
                'num_of_adults'=> 2,
                'num_of_children'=> 2,
                'payment'=> 399,
                'points_added'=> 20,
            ]);
        }
    }
}
