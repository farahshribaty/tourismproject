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
        'num_of_ratings',
        'details',
        'open_at',
        'close_at',
        'available_days',
        'website_url',
        'adult_price',
        'child_price',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    public function type()
    {
        return $this->belongsTo(AttractionType::class,'attraction_type_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class,'city_id');
    }

    public function photos()
    {
        return $this->hasMany(AttractionPhoto::class,'attraction_id');
    }

    public function photo()
    {
        return $this->hasOne(AttractionPhoto::class,'attraction_id')->latest('id');
    }

    public function reviews()
    {
        return $this->belongsToMany(User::class,AttractionReview::class,'attraction_id','user_id');
    }
}
