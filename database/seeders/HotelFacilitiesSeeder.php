<?php

namespace Database\Seeders;

use App\Models\HotelsFacilities;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelFacilitiesSeeder extends Seeder
{
    public function run(): void
    {
        for($i = 0 ; $i<20 ; $i++){
         HotelsFacilities::create([
        'hotel_id'=>random_int(1,17),
        'facilities_id'=>random_int(1,6)
        ]);
        } 
    }
}
