<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomFeatures;
use App\Models\RoomPhotos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 0 ; $i<30 ; $i++){

        Room::create([
        'room_type'=>random_int(1,9),
        'hotel_id'=>random_int(1,17),
        'details'=>'Central hotel is a family run Business
         conveniently located in the heart of central London',
        'price_for_night'=>random_int(100,500),
        'rate'=>random_int(1,5),
        'num_of_ratings'=> random_int(10,3000),
        ]);
        }

        for($i = 0 ; $i<30 ; $i++)
        {
            RoomFeatures::create([
                'room_id'=>random_int(2,18),
                'features_id'=>random_int(1,5)
                ]);
        }

        $rooms = Room::get();

        foreach($rooms as $room){
            RoomPhotos::create([
                'room_id'=>$room['id'],
                'path'=>'http://127.0.0.1:8000/images/room/'.'1685731783.jpg',
            ]);
        }
    }
}
