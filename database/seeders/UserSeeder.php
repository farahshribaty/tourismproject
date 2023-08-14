<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 0 ; $i<20 ; $i++){
            User::create([
                'first_name'=>fake()->name(),
                'last_name'=>fake()->name(),
                'email'=>fake()->name().'@gmail.com',
                'password'=>fake()->name(),
                'phone_number'=>fake()->phoneNumber(),
                'wallet'=> 2000,
                'points'=>20,
            ]);
        }

        User::create([
            'first_name'=> 'mohamad',
            'last_name'=> 'qattan',
            'email'=> 'mohamad@gmail.com',
            'password'=> 'mohamadm',
            'phone_number'=> '0999999999',
            'wallet'=> 3000,
            'points'=> 30,
        ]);

    }
}
