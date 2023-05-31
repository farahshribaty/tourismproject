<?php

namespace Database\Seeders;

use App\Models\AttractionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttractionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AttractionType::create([
            'type'=>'Natural attraction',
            'details'=>'These are attractions that are naturally occurring in the environment, such as mountains, beaches, waterfalls, and national parks.'
        ]);
        AttractionType::create([
            'type'=>'Cultural attraction',
            'details'=>'These are attractions that are related to the culture and history of a place, such as museums, historical sites, and monuments.'
        ]);
        AttractionType::create([
            'type'=>'Entertainment attraction',
            'details'=>'These are attractions that are primarily designed for entertainment purposes, such as amusement parks, zoos, and aquariums.'
        ]);
        AttractionType::create([
            'type'=>'Sports Attraction',
            'details'=>'Entertainment attractions: These are attractions that are primarily designed for entertainment purposes, such as amusement parks, zoos, and aquariums.'
        ]);
        AttractionType::create([
            'type'=>'Urban Attraction',
            'details'=>'These are attractions that are related to sports and physical activities, such as ski resorts, golf courses, and sports stadiums.'
        ]);
        AttractionType::create([
            'type'=>'Religious Attraction',
            'details'=>'These are attractions that are related to religion and spirituality, such as temples, churches, and pilgrimage sites.'
        ]);
        AttractionType::create([
            'type'=>'Adventure Attraction',
            'details'=>'These are attractions that offer adventure activities such as bungee jumping, zip-lining, and rock climbing.'
        ]);
        AttractionType::create([
            'type'=>'Eco_tourism',
            'details'=>'These are attractions that are designed to promote sustainable tourism and preserve natural environments, such as wildlife reserves and eco-lodges.'
        ]);

    }
}
