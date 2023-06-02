<?php

namespace Database\Seeders;

use App\Models\Facilities;
use App\Models\Features;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Validation\Rules\Enum;

class FacilitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $activities = [
        'Yoga Classes ','Spa Services ',' Movie Nights','Local Tours','Bike Rentals',
        ' Game Nights for Kids '
        ];
        $meals = ['Breakfast','Lunch','Dinner','Other','snack','child meal'];

       for($i = 0 ; $i<6 ; $i++)
       {
         Facilities::create([
         'Wifi'=>random_int(0,1),
         'Parking'=>random_int(0,1),
         'Transportation'=>random_int(0,1),
         'Formalization'=>random_int(0,1),
         'activities'=>$activities[$i],
         'meals'=>$meals[$i]
         ]);
         
         Features::create([
        'Housekeeping'=>random_int(0,1),
        'Telephone'=>random_int(0,1),
        'Wake-up service'=>random_int(0,1),
        'Private bathrooms'=>random_int(0,1),
        'Hair dryer'=>random_int(0,1),
       ]);
       }

       
        
    }
}
