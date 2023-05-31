<?php

namespace Database\Seeders;

use App\Models\Types;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        Types::create([
            'name'=>'Chain'
        ]);
        Types::create([
            'name'=>'Motel'
        ]);
        Types::create([
            'name'=>'Resorts'
        ]);
        Types::create([
            'name'=>'Inns'
        ]);
        Types::create([
            'name'=>'All-suites'
        ]);
    }
}
