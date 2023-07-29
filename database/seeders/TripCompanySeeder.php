<?php

namespace Database\Seeders;

use App\Models\TripCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        for($i=0 ; $i<=17 ; $i++){
            TripCompany::create([
                'name'=>'company number '.$i,
                'email'=>'company'.$i.'@gmail.com',
                'phone_number'=>34534543,
                'country_id'=>($i%3)+1,
            ]);
        }
    }
}
