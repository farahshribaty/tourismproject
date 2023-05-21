<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Attraction extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'city_id',
        'attraction_type_id',
        'name',
        'email',
        'password',
        'location',
        'phone_number',
        'rete',
        'open_at',
        'close_at',
        'available_days',
        'website_url',
        'adult_price',
        'child_price',
    ];

    protected $hidden = [
        'password',
    ];
}
