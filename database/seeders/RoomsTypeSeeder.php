<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RoomType::create([
           'name'=>'Villa'
        ]);
        RoomType::create([
            'name'=>'Suiet'
        ]);
        RoomType::create([
            'name'=>'Adjacent rooms'
        ]);
        RoomType::create([
            'name'=>'Single rooms'
        ]);
        RoomType::create([
            'name'=>'Connected room'
        ]);
        RoomType::create([
            'name'=>'Accessible room'
        ]);
        RoomType::create([
            'name'=>'Cabana room'
        ]);
        RoomType::create([
            'name'=>'Smoking room'
        ]);
        RoomType::create([
            'name'=>'Non-Smoking room'
        ]);
    }
}
